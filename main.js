// Modules to control application life and create native browser window
const { app, BrowserWindow } = require("electron");
const path = require("path");
const ipc = require("electron").ipcMain;

function createWindow() {
  // Create the browser window.
  const mainWindow = new BrowserWindow({
    autoHideMenuBar: true,
    icon: path.join(__dirname, "/assets/icons/win/icon.ico"),
    width: 1360,
    height: 1024,
    modal: true,
    fullscreen: true,
    webPreferences: {
      preload: path.join(__dirname, "preload.js"),
      nodeIntegration: true,
      contextIsolation: false,
      enableRemoteModule: true,
    },
  });


  const electronLocalshortcut = require("electron-localshortcut");

  ipc.on("notes", (event, data) => {
    accessToken = data;

    mainWindow.webContents.send("got-access-token", accessToken);
    event.sender.send("ok", "Hello World!");
  });

  ipc.on("get", (event) => {

    mainWindow.webContents.send("get-token");
  });

  ipc.on("delete", (event) => {

    mainWindow.webContents.send("delete-token");
  });

  const editwindow = new BrowserWindow({
    autoHideMenuBar: true,
    parent: mainWindow,
    height: 700,
    show: false,
    webPreferences: {
      nodeIntegration: true,
      contextIsolation: false,
      enableRemoteModule: true,
    },
  });


 
  ipc.on("edit", (event, data) => {
    accessToken = data;
    editwindow.show();
    editwindow.webContents.send("send-token", accessToken);
  });

  electronLocalshortcut.register(mainWindow, "Escape", () => {
    mainWindow.close();
  });


  
  mainWindow.loadFile("src/index.html");
  // mainWindow.setIcon("assets/icons/win/icon.ico");
}

app.whenReady().then(() => {
  createWindow();

  app.on("activate", function () {
    if (BrowserWindow.getAllWindows().length === 0) createWindow();
  });
});

app.on("window-all-closed", function () {
  if (process.platform !== "darwin") app.quit();
});
