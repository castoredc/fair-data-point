import 'core-js/stable';
import 'regenerator-runtime/runtime';

import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter as Router } from 'react-router-dom';
import App from './components/App';
import TagManager from 'react-gtm-module';
import { SnackbarProvider } from 'notistack';

TagManager.initialize({
    gtmId: 'GTM-MPCWGSR',
});

const root = ReactDOM.createRoot(document.getElementById('root')!);

root.render(
    <Router>
        <SnackbarProvider
            autoHideDuration={5000}
        >
            <App />
        </SnackbarProvider>
    </Router>);
