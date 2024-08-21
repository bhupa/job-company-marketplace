import { Socket, io } from 'socket.io-client';

const { VITE_CHAT_SERVER_HOST_URL } = import.meta.env;

/**
 * Run this command to see if socket server is running
 * curl "https://node.jobmatchy.net/socket.io/?EIO=4&transport=polling"
 */

const socketUrl = VITE_CHAT_SERVER_HOST_URL;
let socket: Socket;

const connectSocket = (token: string, userId: string) => {
  const options = {
    query: {
      userToken: `Bearer ${token}`,
      userId: userId,
    },
    transports: ['websocket'],
  };

  socket = io(socketUrl, options);

  socket.on('connect', () => {
    // console.log('Connected to socket');
  });

  socket.on('disconnect', reason => {
    if (reason === 'io server disconnect') {
      // the disconnection was initiated by the server, you need to reconnect manually
      socket.connect();
    }
    // else the socket will automatically try to reconnect
    console.log('Disconnected from socket');
  });

  socket.on('connect_error', e => {
    console.log('Socket connection error ', e);
  });

  return socket;
};

const getSocket = () => {
  return socket;
};

export { connectSocket, getSocket };
