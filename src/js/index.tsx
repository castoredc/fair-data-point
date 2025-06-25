import 'core-js/stable';
import 'regenerator-runtime/runtime';

import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router } from 'react-router-dom';
import { AppProvider } from '@castoredc/matter';
import App from './components/App';
import { edcTheme } from '@castoredc/matter-utils';
import TagManager from 'react-gtm-module';

TagManager.initialize({
    gtmId: 'GTM-MPCWGSR',
});

ReactDOM.render(
    <Router>
        <AppProvider theme={edcTheme}>
            <App />
        </AppProvider>
    </Router>,
    document.getElementById('root')
);
