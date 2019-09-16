import React from "react";
import ReactDOM from "react-dom";
import HookedCarousel from "./HookedCarousel";

const App = () => <HookedCarousel />;

const rootElement = document.getElementById("root");
ReactDOM.render(<HookedCarousel />, rootElement);
