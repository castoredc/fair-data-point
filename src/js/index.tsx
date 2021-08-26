import "core-js/stable";
import "regenerator-runtime/runtime";

import React from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter as Router} from 'react-router-dom';
import {AppProvider} from '@castoredc/matter';
import App from "./components/App";

ReactDOM.render(
    <Router>
        <AppProvider>
            <App />
        </AppProvider>
    </Router>,
    document.getElementById('root')
);
