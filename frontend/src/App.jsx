import React from 'react';
import { BrowserRouter, Switch, Route } from 'react-router-dom';
import './App.css';

import Header from './components/Header/Header';
import Profile from './containers/Profile/Profile';
import Register from './containers/User/Register/Register';
import LoginContainer from './containers/User/Login/LoginContainer';
import Home from './containers/Home/Home';

function App() {

  const showHeader = (path) => {
    if (path.includes('register') || path.includes('login')) {
      return false;
    }
  }

  return (
    <div className="App">   
      <BrowserRouter>
        { showHeader(window.location.href) ? <Header/> : '' }
        <Switch>
          <Route path='/profile' component={Profile} exact />
          <Route path='/register' component={Register} exact />
          <Route path='/login' component={LoginContainer} exact />
          <Route path='' component={Home} exact />
        </Switch>
      </BrowserRouter>
    </div>
  );
}

export default App;