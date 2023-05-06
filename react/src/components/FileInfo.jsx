import React from 'react';
import { PropTypes } from 'prop-types';

const FileInfo = ({ children, filename, filesize }) => {
  const filenameRoot = filename.slice(0, -4);
  let roundedFilesize = Math.ceil(Number(filesize) / 1000);
  roundedFilesize =
    roundedFilesize > 1000
      ? Math.ceil(Number(roundedFilesize) / 1000).toString() + 'MB'
      : roundedFilesize.toString() + 'KB';

  return (
    <div>
      <table className="filebox">
        <tbody>
          <tr>
            <td className="filepng">
              <img src="./images/file.png" alt="PDF File" />
            </td>
            <td>
              <label>{filenameRoot}</label>
              <div className="grey">{roundedFilesize}</div>
            </td>
            {children}
          </tr>
        </tbody>
      </table>
    </div>
  );
};

export default FileInfo;

FileInfo.propTypes = {
  children: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.node),
    PropTypes.node
  ]),
  filename: PropTypes.string.isRequired,
  filesize: PropTypes.string.isRequired,
};

FileInfo.defaultProps = {
  filename: '',
  filesize: '',
};
