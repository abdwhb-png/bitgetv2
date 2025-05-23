import Pusher from "pusher-js";

// Const
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCulster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

/*Pusher*/

window.Pusher = Pusher;
// window.Pusher.logToConsole = true;
