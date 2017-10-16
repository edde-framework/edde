import {e3} from "./e3";
import {ICollection} from "./collection";
import {IElement} from "./protocol";

export interface IJobManager {
	/**
	 * enqueue the given element to be executed as a job
	 *
	 * @param element
	 */
	queue(element: IElement): IJobManager;

	/**
	 * execute and clear current job queue
	 */
	execute(): IJobManager;
}

export class JobManager implements IJobManager {
	protected jobQueue: ICollection<IElement> = e3.collection<IElement>();
	protected queueId: any;

	/**
	 * @inheritDoc
	 */
	public queue(element: IElement): IJobManager {
		this.jobQueue.add(element);
		return this;
	}

	/**
	 * @inheritDoc
	 */
	public execute(): IJobManager {
		if (this.jobQueue.isEmpty()) {
			return this;
		}
		if (this.queueId) {
			setTimeout(() => this.execute(), 300);
			return this;
		}
		const jobQueue = e3.collection<IElement>().copy(this.jobQueue);
		this.jobQueue.clear();
		this.queueId = setTimeout(() => {
			jobQueue.each(element => e3.execute(element));
			jobQueue.clear();
			this.queueId = null;
			this.execute();
		}, 0);
		return this;
	}
}
