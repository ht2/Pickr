/**
* @author Erik Karlsson, www.nonobtrusive.com
*/
function Dispatcher(){
	this.events=[];
}
Dispatcher.prototype.addEventlistener=function(event,callback){
	this.events[event] = this.events[event] || [];
	if ( this.events[event] ) {
		this.events[event].push(callback);
	}
}
Dispatcher.prototype.removeEventlistener=function(event,callback){
	if ( this.events[event] ) {
		var listeners = this.events[event];
		for ( var i = listeners.length-1; i>=0; --i ){
			if ( listeners[i] === callback ) {
				listeners.splice( i, 1 );
				return true;
			}
		}
	}
	return false;
}

Dispatcher.prototype.dispatch = function(event, data)
{
	// default val for data;
	data = data || {};

	if(this.events[event])
	{
		var listeners = this.events[event];
		var len = listeners.length;

		while(len--)
		{
			listeners[len](data);
		}
	}
}