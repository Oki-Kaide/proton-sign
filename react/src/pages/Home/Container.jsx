import React from 'react';
import Home from '.';
import { AppContext } from '../../components/Provider';
import UploadDocContainer from '../UploadDoc/Container';

class HomeContainer extends React.Component {
  static contextType = AppContext;

  constructor(props) {
    super(props);
    this.state = {
      windowWidth: 0,
      isLoggingIn: false,
    };
  }

  componentDidMount() {
    this.updateWindowWidth();
    window.addEventListener('resize', this.updateWindowWidth);
  }

  updateWindowWidth = () => {
    this.setState({ windowWidth: window.innerWidth });
  };

  render() {
    const { windowWidth, isLoggingIn } = this.state;
    const { history } = this.props;
    if (this.context.actor) {
      return (
        <UploadDocContainer history={history} />
      );
    }

    return (
      <Home
        windowWidth={windowWidth}
        isLoggingIn={isLoggingIn}
      />
    );
  }
}

export default HomeContainer;
