import React from 'react';
import axios from 'axios';
import UploadDoc from '.';
import { AppContext } from '../../components/Provider';

class UploadDocContainer extends React.Component {
  static contextType = AppContext;

  constructor(props) {
    super(props);
    this.state = {
      isLoggingIn: false,
    };
  }

  onFileUpload = (acceptedFiles) => {
    const { history } = this.props;
    const { actor, setDocInfo } = this.context;
    const formData = new FormData();
    formData.append('sa', actor);
    formData.append('fileToUpload', acceptedFiles[0], acceptedFiles[0].name);

    axios
      .post('/psignapi/upload.php', formData)
      .then((res) => {
        const isValidResponse = typeof res === 'object' && res !== null && 'data' in res;
        if (isValidResponse) {
          const data = res.data;
          const isObject = typeof data === 'object' && data !== null;
          const isError = isObject && 'error' in data;
          const isResult = isObject && 'result' in res.data;

          if (isError) {
            alert('error: ' + data['error']);
          }

          if (isResult) {
            let docInfo = {
              id: data['result']['docrequestid'],
              hash: data['result']['hash'],
              filename: data['result']['filename'],
              filesize: data['result']['filesize'],
            };
            setDocInfo(docInfo);
            history.push({ pathname: '/addsigners' });
          }
        } else {
          console.warn('unsuccessful post');
        }
      })
      .catch((err) => console.error(err));
  };

  render() {
    return (
      <UploadDoc onFileUpload={this.onFileUpload} />
    );
  }
}

export default UploadDocContainer;
