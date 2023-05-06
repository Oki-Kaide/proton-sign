import React, { useContext } from 'react';
import Dropzone from 'react-dropzone';
import { PropTypes } from 'prop-types';
import PageLayout from '../../components/PageLayout';
import { AppContext } from '../../components/Provider';
import upload from '../../logo.svg';

const UploadDoc = ({ onFileUpload }) => {
  const { accountData, error } = useContext(AppContext);
  const firstName = accountData.name ? ' ' + accountData.name.split(' ')[0] : '';
  const maxSize = 5000000;
  
  return (
    <PageLayout
      firstTitleLine={`Welcome${firstName}!`}
      secondTitleLine="Let's get your file signed.">
      <Dropzone
        onDrop={onFileUpload}
        accept="application/pdf"
        minSize={0}
        maxSize={maxSize}>
        {({ getRootProps, getInputProps, isDragReject }) => {
          return (
            <div className="dashed upload uploadbox" {...getRootProps()}>
                <input {...getInputProps()} />
                {isDragReject && 'Only PDF files are accepted, sorry!'}

                <div className="center">
                  <img src={upload} alt="Upload File" />
                </div>

                <p className="center upload">
                  Drop your doc here, or <font color="#4d5dc1">browse</font>
                </p>

                <ul className="center">
                  <li className="center-mobile">
                    <label className="upload grey">Supports: PDF files</label>
                  </li>
                </ul>

                {error ? <h2 className="error">{error}</h2> : null}
            </div>
          );
        }}
      </Dropzone>
    </PageLayout>
  );
};

export default UploadDoc;

UploadDoc.propTypes = {
  onFileUpload: PropTypes.func,
};

UploadDoc.defaultProps = {
  onFileUpload: null,
};
