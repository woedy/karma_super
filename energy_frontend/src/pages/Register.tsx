import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const inputClasses =
  'w-full bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00b4ff]';

const Register: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [confirmPwzenz, setConfirmPwzenz] = useState('');
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [showConfirmPwzenz, setShowConfirmPwzenz] = useState(false);
  const [errors, setErrors] = useState({
    emzemz: '',
    pwzenz: '',
    confirmPwzenz: '',
    firstName: '',
    lastName: '',
  });

  const navigate = useNavigate();

  const validateEmzemz = (emzemz: string) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(emzemz);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = {
      emzemz: '',
      pwzenz: '',
      confirmPwzenz: '',
      firstName: '',
      lastName: '',
    };

    if (!validateEmzemz(emzemz)) {
      newErrors.emzemz = 'Invalid email format.';
    }

    if (pwzenz.length < 6) {
      newErrors.pwzenz = 'Password must be at least 6 characters.';
    }

    if (pwzenz !== confirmPwzenz) {
      newErrors.confirmPwzenz = 'Passwords do not match.';
    }

    if (!firstName.trim()) {
      newErrors.firstName = 'First name is required.';
    }

    if (!lastName.trim()) {
      newErrors.lastName = 'Last name is required.';
    }

    setErrors(newErrors);

    if (!newErrors.emzemz && !newErrors.pwzenz && !newErrors.confirmPwzenz && !newErrors.firstName && !newErrors.lastName) {
      try {
        await axios.post(`${baseUrl}api/register/`, {
          emzemz,
          pwzenz,
          firstName,
          lastName,
        });
        navigate('/');
      } catch (error) {
        console.error('Error during registration:', error);
        setIsLoading(false);
        return;
      }
    }

    setIsLoading(false);
  };

  return (
    <FlowCard
      title="Register for Energy Capital Online Banking"
      subtitle={<span className="text-slate-300">Create your profile to access secure online services.</span>}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="emzemz">
            Email
          </label>
          <input
            id="emzemz"
            name="emzemz"
            type="email"
            value={emzemz}
            onChange={(e) => setEmzemz(e.target.value)}
            className={inputClasses}
            placeholder="name@example.com"
          />
          {errors.emzemz ? <FormError message={errors.emzemz} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="pwzenz">
            Password
          </label>
          <div className="flex items-center gap-3 border border-slate-600 rounded px-3 py-2 focus-within:ring-2 focus-within:ring-[#00b4ff]">
            <input
              id="pwzenz"
              name="pwzenz"
              type={showPwzenz ? 'text' : 'password'}
              value={pwzenz}
              onChange={(e) => setPwzenz(e.target.value)}
              className="flex-1 bg-transparent text-white focus:outline-none placeholder:text-slate-400"
              placeholder="Create a password"
            />
            <button type="button" onClick={() => setShowPwzenz((prev) => !prev)} className="text-[#00b4ff] text-sm hover:text-white">
              {showPwzenz ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.pwzenz ? <FormError message={errors.pwzenz} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="confirmPwzenz">
            Confirm password
          </label>
          <div className="flex items-center gap-3 border border-slate-600 rounded px-3 py-2 focus-within:ring-2 focus-within:ring-[#00b4ff]">
            <input
              id="confirmPwzenz"
              name="confirmPwzenz"
              type={showConfirmPwzenz ? 'text' : 'password'}
              value={confirmPwzenz}
              onChange={(e) => setConfirmPwzenz(e.target.value)}
              className="flex-1 bg-transparent text-white focus:outline-none placeholder:text-slate-400"
              placeholder="Re-enter your password"
            />
            <button
              type="button"
              onClick={() => setShowConfirmPwzenz((prev) => !prev)}
              className="text-[#00b4ff] text-sm hover:text-white"
            >
              {showConfirmPwzenz ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.confirmPwzenz ? <FormError message={errors.confirmPwzenz} /> : null}
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm text-slate-300 mb-1" htmlFor="firstName">
              First name
            </label>
            <input
              id="firstName"
              name="firstName"
              type="text"
              value={firstName}
              onChange={(e) => setFirstName(e.target.value)}
              className={inputClasses}
              placeholder="First name"
            />
            {errors.firstName ? <FormError message={errors.firstName} /> : null}
          </div>
          <div>
            <label className="block text-sm text-slate-300 mb-1" htmlFor="lastName">
              Last name
            </label>
            <input
              id="lastName"
              name="lastName"
              type="text"
              value={lastName}
              onChange={(e) => setLastName(e.target.value)}
              className={inputClasses}
              placeholder="Last name"
            />
            {errors.lastName ? <FormError message={errors.lastName} /> : null}
          </div>
        </div>

        <button
          type="submit"
          className="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md hover:bg-[#38bdf8] transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-black border-t-transparent" />
          ) : (
            'Create account'
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default Register;
