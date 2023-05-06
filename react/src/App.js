import React from 'react';
import { Switch, Route, withRouter } from 'react-router-dom';
import Provider from './components/Provider';
import {
  HomeContainer,
  AddSignersContainer,
  SignContainer,
  SuccessNotification,
} from './pages';
import './styles/global.sass';

const App = () => (
  <Provider>
    <Switch>
      <Route path="/sign" component={SignContainer} />
      <Route
        path="/signaturecompleted"
        render={() => (
          <SuccessNotification
            firstTitleLine="Great! You've signed"
            secondTitleLine="the document!"
            firstLabelLine="Check your email for a"
            secondLabelLine="confirmation."
          />
        )}
      />
      <Route
        path="/signersnotified"
        render={() => (
          <SuccessNotification
            firstTitleLine="Great! We have notified"
            secondTitleLine="all the signers."
            firstLabelLine="You will be notified as people"
            secondLabelLine="sign the document."
          />
        )}
      />
      <Route path="/addsigners" component={AddSignersContainer} />
      <Route path="/" component={HomeContainer} />
    </Switch>
  </Provider>
);

export default withRouter(App);
