import React, { createContext } from 'react';
import { withRouter } from 'react-router-dom';
import ProtonSDK from '../utils/proton';

export const AppContext = createContext({
  actor: '',
  permission: '',
  session: '',
  error: '',
  accountData: {},
  docInfo: {},
  setLoggedInState: () => {},
  setDocInfo: () => {},
  setErrorState: () => {},
  login: () => {},
  logout: () => {},
});

class Provider extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      actor: '',
      permission: '',
      session: '',
      error: '',
      accountData: {},
      docInfo: {},
    };
  }

  componentDidMount = async () => {
    this.checkIfLoggedIn();
  };

  componentDidUpdate = (_, prevState) => {
    if (prevState.error) {
      this.setErrorState('');
    }
  }

  checkIfLoggedIn = async () => {
    const { auth, accountData, error } = await ProtonSDK.restoreSession();
    const { history } = this.props;

    if (error) {
      this.setErrorState(error);
      return;
    }

    if (auth && auth.actor && auth.permission) {
      this.setLoggedInState(auth.actor, auth.permission, accountData);
    } else {
      if (
        window.location.search.includes('doc') &&
        !window.location.href.includes('/sign')
      ) {
        history.push({
          pathname: '/sign',
          search: window.location.search,
        });
      }
    }
  };

  setDocInfo = async (docInfo) => {
    this.setState({ docInfo });
  };

  setLoggedInState = async (actor, permission, accountData) => {
    this.setState({ actor, permission, accountData });
  };

  setErrorState = (error) => {
    this.setState({ error });
  };

  login = async () => {
    const { auth, accountData, error } = await ProtonSDK.login();
    if (error) {
      this.setErrorState(error);
      return;
    }

    if (auth && auth.actor && auth.permission) {
      this.setLoggedInState(auth.actor, auth.permission, accountData);
    }
  };

  logout = async () => {
    const { accountData } = this.state;
    const { history } = this.props;
    if (accountData && accountData.acc) {
      await ProtonSDK.logout();
      this.setState({ actor: '', accountData: {}, session: '' });
    }
    history.push('/');
  };

  render() {
    const { children } = this.props;
    const contextValue = {
      ...this.state,
      setLoggedInState: this.setLoggedInState,
      setDocInfo: this.setDocInfo,
      logout: this.logout,
      login: this.login
    };

    return (
      <AppContext.Provider value={contextValue}>
        {children}
      </AppContext.Provider>
    );
  }
}

export default withRouter(Provider);
