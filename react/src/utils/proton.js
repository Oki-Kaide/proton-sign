import { ConnectWallet } from '@proton/web-sdk';
import Logo from '../logo.svg';

class ProtonSDK {
  constructor() {
    this.chainId = '384da888112027f0321850a169f737c33e53b388aad48b5adace4bab97f437e0';
    this.endpoints = ['https://proton.greymass.com'];
    this.appName = 'ProtonSign';
    this.requestAccount = 'protonsign';
    this.session = null;
    this.link = null;
  }
  
  connect = async ({ restoreSession }) => {
    const { link, session } = await ConnectWallet({
      linkOptions: {
        chainId: this.chainId,
        endpoints: this.endpoints,
        restoreSession,
      },
      transportOptions: {
        requestAccount: this.requestAccount,
        backButton: true,
      },
      selectorOptions: {
        appName: this.appName,
        appLogo: Logo,
      },
    });
    this.link = link;
    this.session = session;
  };
  
  login = async () => {
    try {
      await this.connect({ restoreSession: false });
      const { auth, accountData } = this.session;
      return {
        auth,
        accountData: accountData ? accountData[0] : {
          name: '',
          acc: auth.actor,
          avatar: '',
        },
      };
    } catch (e) {
      return { error: e.message || "An error has occurred while logging in"};
    }
  };
  
  sendTransaction = async (actions) => {
    try {
      const result = await this.session.transact(
        { actions: actions },
        { broadcast: true }
      );
      return result;
    } catch (e) {
      return { error: e.message || "An error has occurred while sending a transaction"};
    }
  };
  
  logout = async () => {
    await this.link.removeSession(this.requestAccount, this.session.auth);
  };

  restoreSession = async () => {
    try {
      await this.connect({ restoreSession: true });
      if (this.session) {
        const { auth, accountData } = this.session;
        return {
          auth,
          accountData: accountData ? accountData[0] : {
            name: '',
            acc: auth.actor,
            avatar: '',
          },
        };
      }
    } catch (e) {
      return { error: e.message || "An error has occurred while restoring a session"};
    }
    return {
      auth: {
        actor: '',
        permission: ''
      },
      accountData: {}
    };
  };
}

export default  new ProtonSDK();
