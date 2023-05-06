import React, { useContext, useState } from 'react';
import { Formik, Form, Field, FieldArray, ErrorMessage } from 'formik';
import axios from 'axios';
import * as Yup from 'yup';
import PageLayout from '../../components/PageLayout';
import FileInfo from '../../components/FileInfo';
import { AppContext } from '../../components/Provider';

const RemoveSignerButton = ({ isLastSigner, removeSigner }) =>
  isLastSigner ? (
    <td className="remove-signer-button" onClick={removeSigner}>
      <img src="./images/x.png" alt="x-icon" />
    </td>
  ) : null;

const AddSignersContainer = ({ history }) => {
  const { actor, docInfo, error } = useContext(AppContext);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const onAddSigner = (values, setValues) => {
    if (values.signers.length > 4) return;
    const signers = [...values.signers, { name: '', email: '' }];
    setValues({ ...values, signers });
  };

  const onRemoveSigner = (values, setValues) => {
    const signers = [...values.signers.slice(0, values.signers.length - 1)];
    setValues({ ...values, signers });
  };

  const onUpdateSigner = (e, values, setValues, signerNumber, fieldName) => {
    const signers = [...values.signers];
    signers[signerNumber][fieldName] = e.target.value;
    setValues({ ...values, signers });
  };

  const validationSchema = Yup.object().shape({
    signers: Yup.array().of(
      Yup.object().shape({
        name: Yup.string(),
        email: Yup.string().email('Please use a valid email address'),
      })
    ),
  });

  const handleSubmit = async (values) => {
    if (!values.signers[values.signers.length - 1].email) return;
    setIsSubmitting(true);

    const formData = new FormData();

    for (let key in values.signers) {
      formData.append('name' + key, `${values.signers[key].name}`);
      formData.append('email' + key, `${values.signers[key].email}`);
    }

    formData.append('sa', actor);
    formData.append('docrequestid', docInfo.id);

    const res = await axios.post('/psignapi/addsigners.php', formData);
    const isValidResponse =
    typeof res === 'object' && res !== null && 'data' in res;
    const isValidData = typeof res.data === 'object' && res.data !== null;
    const isError = isValidResponse && isValidData && 'error' in res.data;
    const isResult = isValidResponse && isValidData && 'result' in res.data;
    setIsSubmitting(false);

    if (isError) {
      alert('Error: ' + res.data['error']);
      return;
    }

    if (!isResult) {
      alert('Unable to add signers. Please try again.');
      return;
    }

    history.push({
      pathname: '/signersnotified',
    });
  };

  return (
    <PageLayout
      firstTitleLine="Who needs to sign this"
      secondTitleLine="document?">
      <FileInfo
        filename={docInfo.filename}
        filesize={docInfo.filesize.toString()}>
        <td className="right x-icon" onClick={() => history.push('/')}>
          <img src="./images/x.png" alt="x-icon" />
        </td>
      </FileInfo>
      <Formik
        initialValues={{ signers: [{ name: '', email: '' }] }}
        validationSchema={validationSchema}>
        {({ values, setValues }) => (
          <Form>
            <div className="fullwidth add-signer-container">
              <p>Signers</p>
              <button
                className="nomobile grey add-signer"
                onClick={() => onAddSigner(values, setValues)}>
                <img src="./images/addsigner.png" alt="Add Signer" />
                <span>Add signer</span>
              </button>
            </div>

            <FieldArray
              name="signers"
              render={() => (
                <>
                  <table className="nomobile fullwidth">
                    <tbody>
                      <tr>
                        <td>
                          <label className="bold">Name</label>
                        </td>
                        <td className="halfwidth">
                          <label className="bold">Email</label>
                        </td>
                      </tr>
                    </tbody>
                    {values.signers.map((_, index) => (
                      <tbody key={index}>
                        <tr>
                          <td>
                            <Field
                              name={`signers.${index}.name`}
                              placeholder="John Doe"
                              className="signer-form-input"
                              onChange={(e) =>
                                onUpdateSigner(
                                  e,
                                  values,
                                  setValues,
                                  index,
                                  'name'
                                )
                              }
                            />{' '}
                            <ErrorMessage
                              name={`signers.${index}.name`}
                              component="div"
                              className="signer-form-error"
                            />
                          </td>
                          <td>
                            <Field
                              name={`signers.${index}.email`}
                              type="email"
                              placeholder="johndoe@gmail.com"
                              className="signer-form-input"
                              onChange={(e) =>
                                onUpdateSigner(
                                  e,
                                  values,
                                  setValues,
                                  index,
                                  'email'
                                )
                              }
                            />{' '}
                            <ErrorMessage
                              name={`signers.${index}.email`}
                              component="div"
                              className="signer-form-error"
                            />
                          </td>
                        </tr>
                        <RemoveSignerButton
                          isLastSigner={
                            values.signers.length !== 1 &&
                            values.signers.length === index + 1
                          }
                          removeSigner={() => onRemoveSigner(values, setValues)}
                        />
                      </tbody>
                    ))}
                  </table>

                  <div className="mobileonly">
                    {values.signers.map((_, index) => (
                      <div className="signerspacing" key={index}>
                        <label className="bold">Name</label>
                        <Field
                          name={`signers.${index}.name`}
                          placeholder="John Doe"
                          className="signer-form-input"
                          onChange={(e) =>
                            onUpdateSigner(e, values, setValues, index, 'name')
                          }
                        />{' '}
                        <ErrorMessage
                          name={`signers.${index}.name`}
                          component="div"
                          className="signer-form-error"
                        />
                        <label className="bold">Email</label>
                        <Field
                          name={`signers.${index}.email`}
                          type="email"
                          placeholder="johndoe@gmail.com"
                          className="signer-form-input"
                          onChange={(e) =>
                            onUpdateSigner(e, values, setValues, index, 'email')
                          }
                        />{' '}
                        <ErrorMessage
                          name={`signers.${index}.email`}
                          component="div"
                          className="signer-form-error"
                        />
                        <RemoveSignerButton
                          isLastSigner={
                            values.signers.length !== 1 &&
                            values.signers.length === index + 1
                          }
                          removeSigner={() => onRemoveSigner(values, setValues)}
                        />
                      </div>
                    ))}

                    <button
                      className="grey add-signer"
                      onClick={() => onAddSigner(values, setValues)}>
                      <img src="./images/addsigner.png" alt="Add Signer" />
                      <span>Add signer</span>
                    </button>
                  </div>
                </>
              )}
            />

            <button
              className="lavbutton"
              disabled={isSubmitting}
              onClick={() => handleSubmit(values)}>
              Submit for signing
            </button>
            {error ? <h2 className="error">{error}</h2> : null}
          </Form>
        )}
      </Formik>
    </PageLayout>
  );
};

export default AddSignersContainer;
