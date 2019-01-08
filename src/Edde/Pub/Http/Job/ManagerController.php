<?php
	declare(strict_types=1);
	namespace Edde\Pub\Http\Job;

	use Edde\Application\RouterException;
	use Edde\Controller\RestController;
	use Edde\Job\JobSchema;
	use Edde\Runtime\RuntimeException;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Message\MessageBus;
	use Edde\Url\UrlException;
	use Throwable;
	use function json_encode;
	use function microtime;

	class ManagerController extends RestController {
		use JobQueue;
		use MessageBus;

		/**
		 * @throws Throwable
		 * @throws RouterException
		 * @throws RuntimeException
		 * @throws UrlException
		 */
		public function actionExecute(): void {
			$time = microtime(true);
			$job = $this->jobQueue->state($this->getParams()['job'], JobSchema::STATE_RUNNING);
			$state = JobSchema::STATE_SUCCESS;
			$result = null;
			try {
				$this->messageBus->execute(
					$this->messageBus->importMessage(
						$job['message']
					)
				);
			} catch (Throwable $exception) {
				$state = JobSchema::STATE_FAILED;
				$result = $exception->getMessage();
				throw $exception;
			} finally {
				$job['runtime'] = (microtime(true) - $time) * 1000;
				$this->jobQueue->update($job);
				$this->jobQueue->state($job['uuid'], $state, $result);
			}
			$this->textResponse(sprintf('job done [%s]', $job))->execute();
		}

		public function actionCleanup(): void {
			$this->jobQueue->cleanup();
			$this->textResponse('ok')->execute();
		}

		public function actionStats(): void {
			$stats = $this->jobQueue->stats();
			$config = [
				'Enqueued'        => [
					'diff'     => true,
					'positive' => null,
				],
				'Reset'           => [
					'display'  => false,
					'diff'     => false,
					'positive' => null,
				],
				'Scheduled'       => [
					'diff'     => true,
					'positive' => 1,
					'abs'      => true,
				],
				'Running'         => [
					'diff'     => true,
					'positive' => null,
				],
				'Success'         => [
					'diff'     => true,
					'positive' => 1,
				],
				'Failed'          => [
					'diff'     => true,
					'positive' => -1,
				],
				'Sum'             => [
					'diff'     => true,
					'positive' => null,
				],
				'Finished'        => [
					'diff'     => true,
					'positive' => null,
				],
				'Average'         => [
					'diff'     => false,
					'positive' => null,
				],
				'Shortest'        => [
					'diff'     => false,
					'positive' => null,
				],
				'Longest'         => [
					'diff'     => false,
					'positive' => null,
				],
				'AvgRuntime'      => [
					'diff'     => true,
					'positive' => -1,
				],
				'ShortestRuntime' => [
					'diff'     => true,
					'positive' => null,
				],
				'LongestRuntime'  => [
					'diff'     => true,
					'positive' => null,
				],
				'Progress'        => [
					'diff'     => true,
					'positive' => 1,
				],
				'Stats'           => [
					'ignore' => true,
				],
			];
			$refresh = 5;
			$html = '
				<!DOCTYPE html>
				<html lang="en">
					<head>
						<title>Job Stats</title>
						<link rel="icon" href="data:;base64,iVBORw0KGgo=">
						<meta http-equiv="refresh" content="' . $refresh . '">
						<style>
							html * {
								box-sizing: border-box;
							}
						
							.container {
								width: 64%;
								margin: 0 auto;
							}
							
							#stats {
								width: 100%;
								margin-bottom: 12px;
							}
							
							#stats .stat-name {
								width: 17.5%;
							}
							
							#stats .stat-current, #stats th.stat-previous {
								width: 37.5%;
							}

							#stats .stat-diff {
								width: 7.5%;
							}							
							
							#stats tbody tr {
								cursor: pointer;
							}
							
							#stats tbody tr:nth-child(even) {
								background-color: #EDEDED;
							}
							
							#stats tbody tr:nth-child(odd) {
								background-color: #DDD;
							}
							
							#stats tbody tr.good {
								background-color: #6aff20;
							}
							
							#stats tbody tr.bad {
								background-color: #ff8120;
							}
							
							#stats tbody tr:hover {
								background-color: #CCC;
							}
							
							#stats tbody td {
								padding: 4px 6px;
							}
							
							.progressbar-wrap {
								border: 1px solid #333;							
							}
							
							.progressbar {
								height: 64px;
								background-color: #73ff67;
							}
						</style>
					</head>
					<body>
						<div class="container">
							<h1>Job Stats</h1>
							<h4>' . date('Y-m-d H:i:s') . ', auto-refresh by ' . $refresh . 'sec</h4>
							<table id="stats">
								<thead>
									<tr>
										<th class="stat stat-name"><span>Name</span></th>
										<th class="stat stat-current"><span>Current</span></th>
										<th class="stat stat-previous"><span>Previous</span></th>
										<th class="stat stat-diff"><span>Diff</span></th>
									</tr>
								</thead>
								<tbody id="stats-root">
								</tbody>
							</table>
							<div class="progressbar-wrap">
								<div class="progressbar" style="width: ' . $stats['Progress'] . '%"></div>
							</div>
						</div>
						<script>
							const previous = JSON.parse(window.localStorage.getItem("previous") || "{}");
							const current = ' . json_encode($stats) . ';
							const config = ' . json_encode($config) . ';
							const root = document.querySelector("#stats-root");
							for (var k in current) {
								const row = document.createElement("tr");
								const section = config[k];
								if (section.ignore){
									continue;
								}
								
								let diff = false;
								if (section.diff === true && previous[k]) {
									diff = current[k] - previous[k];
								}
								if(diff) {
									if(section.abs === true) {
										diff = Math.abs(diff);
									}
									row.classList.add(section.positive !== null ? ((diff * section.positive) >= 0 ? "good" : "bad") : "dunno");
								}
								row.innerHTML = `
									<td class="stat-name"><span>${k}</span></td>
									<td class="stat-current"><span>${current[k]}</span></td>
									<td class="stat-previous"><span>${previous[k] || "-"}</span></td>
									<td class="stat-diff"><span>${diff !== false ? diff : "-"}</span></td>
								`;
								root.appendChild(row);
							}
							window.localStorage.setItem("previous", JSON.stringify(current));
						</script>
					</body>
				</html>
			';
			$this->htmlResponse($html)->execute();
		}
	}
