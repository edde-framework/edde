export class Listen {
	public static To(event: string | null, weight: number = 100, cancelable: boolean = true): (target: any, property: string) => void {
		return (target: any, property: string) => {
			const name = '::ListenerList/' + event + '::' + property;
			(target[name] = target[name] || []).push({
				'event': event,
				'handler': property,
				'weight': weight,
				'context': null,
				'scope': null,
				'cancelable': cancelable,
			})
		};
	}

	public static ToNative(event: string): (target: any, property: string) => void {
		return (target: any, property: string): void => {
			const name = '::NativeListenerList/' + event + '::' + property;
			(target[name] = target[name] || []).push({
				'event': event,
				'handler': property,
			})
		};
	}
}
