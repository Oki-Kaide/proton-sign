import React from 'react';
import { PropTypes } from 'prop-types';
import PageLayout from '../../components/PageLayout';

const Success = ({
  firstTitleLine,
  secondTitleLine,
  firstLabelLine,
  secondLabelLine
}) => (
  <PageLayout
    firstTitleLine={firstTitleLine}
    secondTitleLine={secondTitleLine}>
    <div className="uploadbox center">
      <img src="./images/check.png" alt="Success!" className="success" />
      <p>Success!!</p>
      <label className="grey">
        {firstLabelLine}
        <br />
        {secondLabelLine}
      </label>
    </div>
  </PageLayout>
);

export default Success;

Success.propTypes = {
  firstTitleLine: PropTypes.string.isRequired,
  secondTitleLine: PropTypes.string.isRequired,
  firstLabelLine: PropTypes.string.isRequired,
  secondLabelLine: PropTypes.string.isRequired,
};

Success.defaultProps = {
  firstTitleLine: '',
  secondTitleLine: '',
  firstLabelLine: '',
  secondLabelLine: '',
};
