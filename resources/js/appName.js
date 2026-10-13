// The name of the site. A module rather than a global on window, so it is also available during server-side rendering.
const appName = import.meta.env.VITE_APP_NAME || "Omdat We Reizen";

export default appName;
