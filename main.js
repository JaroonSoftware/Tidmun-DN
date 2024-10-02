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
    // fullscreen: true,
    webPreferences: {
      preload: path.join(__dirname, "preload.js"),
      nodeIntegration: true,
      contextIsolation: false,
      enableRemoteModule: true,
    },
  });

  function showLoginWindow() {
    const child = new BrowserWindow({
      icon: path.join(__dirname, "/assets/icons/win/icon.ico"),
      autoHideMenuBar: true,
      parent: mainWindow,
      height: 1000,
      width: 1000,
      webPreferences: {
        nodeIntegration: true,
        contextIsolation: false,
        enableRemoteModule: true,
      },
    });
    // child.setIcon('assets/icons/win/icon.ico');
    child.loadFile("src/modal/modal_saleorder.html");
  }
  function showLoginWindow2() {
    const child = new BrowserWindow({
      icon: path.join(__dirname, "/assets/icons/win/icon.ico"),
      autoHideMenuBar: true,
      parent: mainWindow,
      height: 700,
      width: 1000,
      webPreferences: {
        nodeIntegration: true,
        contextIsolation: false,
        enableRemoteModule: true,
      },
    });
    // child.setIcon('assets/icons/win/icon.ico');
    child.loadFile("src/modal/modal_examine.html");
  }

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

  const createDN = new BrowserWindow({
    autoHideMenuBar: true,
    icon: path.join(__dirname, "/assets/icons/win/icon.ico"),
    parent: mainWindow,
    height: 700,
    width: 1000,
    show: false,
    webPreferences: {
      nodeIntegration: true,
      contextIsolation: false,
      enableRemoteModule: true,
    },
  });
  createDN.loadFile("src/modal/modal_delivery.html");
  
  createDN.on("close", (evt) => {
    evt.preventDefault(); // This will cancel the close
    createDN.hide();
  });

  ipc.on("createDN", (event, data) => {
    accessToken = data;
    createDN.show();
    createDN.webContents.send("send-token", accessToken);
  });

  ipc.on("submitDN", (event, data) => {
    
    mainWindow.webContents.send("submit-dn",data);
  });

  ipc.on("message:loginShow", () => {
    showLoginWindow();
  });
  ipc.on("message:loginShow2", () => {
    showLoginWindow2();
  });

  const electronLocalshortcut = require("electron-localshortcut");

  electronLocalshortcut.register(mainWindow, "Escape", () => {
    
    mainWindow.close();
    app.quit();
  });

  showLoginWindow();
  
  mainWindow.loadFile("src/index.html");
  mainWindow.maximize();
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

app.on('will-quit', function () {
  // This is a good place to add tests insuring the app is still
  // responsive and all windows are closed.
  // console.log("will-quit");
  mainWindow = null;
});