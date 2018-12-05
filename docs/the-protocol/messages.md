# Messages

This section describes various types of messages found on *The Protocol*.

## Noop

Quite strange type of message used as response to messages without response; it could just hold "ack" or whatever message
telling the stuff was "acknowledged" but no response has been provided.

This message should be used just for that purpose meaning no service should effectively process that message. 

## State

Message type optimized for state querying; state itself is usually computed from the source model of an application - that
means state could be result of query across a database or just some simple value.

Originally this message type was added because of publishing changes on frontend (or basically any remote system).
