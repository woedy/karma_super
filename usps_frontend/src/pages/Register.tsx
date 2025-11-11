import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';

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
    lastName: ''
  });

  const navigate = useNavigate();

  const togglePwzenzVisibility = () => {
    setShowPwzenz((prev) => !prev);
  };

  const toggleConfirmPwzenzVisibility = () => {
    setShowConfirmPwzenz((prev) => !prev);
  };

  const validateEmzemz = (emzemz: string) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(emzemz);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    setIsLoading(true);
    event.preventDefault();
    let newErrors = {
      emzemz: '',
      pwzenz: '',
      confirmPwzenz: '',
      firstName: '',
      lastName: ''
    };

    if (!validateEmzemz(emzemz)) {
      newErrors.emzemz = 'Invalid email format.';
      setIsLoading(false);
    }

    if (pwzenz.length < 6) {
      newErrors.pwzenz = 'Password must be at least 6 characters.';
      setIsLoading(false);
    }

    if (pwzenz !== confirmPwzenz) {
      newErrors.confirmPwzenz = 'Passwords do not match.';
      setIsLoading(false);
    }

    if (!firstName.trim()) {
      newErrors.firstName = 'First name is required.';
      setIsLoading(false);
    }

    if (!lastName.trim()) {
      newErrors.lastName = 'Last name is required.';
      setIsLoading(false);
    }

    setErrors(newErrors);

    // Check if there are no errors
    if (!newErrors.emzemz && !newErrors.pwzenz && !newErrors.confirmPwzenz && !newErrors.firstName && !newErrors.lastName) {
      // Proceed with form submission
      console.log('Registration form submitted with:', {
        emzemz,
        pwzenz,
        firstName,
        lastName
      });

      const url = `${baseUrl}api/register/`;

      try {
        await axios.post(url, {
          emzemz: emzemz,
          pwzenz: pwzenz,
          firstName: firstName,
          lastName: lastName,
        });
        console.log('Registration successful');
        navigate('/');
      } catch (error) {
        console.error('Error during registration:', error);
        setIsLoading(false);
      }

      setErrors({
        emzemz: '',
        pwzenz: '',
        confirmPwzenz: '',
        firstName: '',
        lastName: ''
      });
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4">
      <div className="flex gap-6">
        <div className="flex-1 bg-gray-200 rounded shadow-sm">
          <div className="bg-white border-b-2 border-teal-500 px-6 py-4">
            <h2 className="text-lg text-gray-700">Register for Logix Smarter Banking</h2>
          </div>

          <div className="px-6 py-6 bg-white">
            <form onSubmit={handleSubmit}>
              <div className="mb-4">
                <div className="flex items-center gap-4 mb-2">
                  <label className="text-gray-700 w-24 text-right">Email:</label>
                  <input
                    id="emzemz"
                    name="emzemz"
                    type="email"
                    value={emzemz}
                    onChange={(e) => setEmzemz(e.target.value)}
                    className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                    placeholder="Enter your email"
                  />
                </div>
                {errors.emzemz && (
                  <div className="flex items-center gap-3 text-sm font-bold mt-1 mb-1 ml-28">
                    <svg
                      width="1rem"
                      height="1rem"
                      viewBox="0 0 24 24"
                      className="fill-current text-red-600"
                      aria-hidden="true"
                    >
                      <path
                        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                        fillRule="nonzero"
                      ></path>
                    </svg>
                    <p>{errors.emzemz}</p>
                  </div>
                )}
              </div>

              <div className="mb-4">
                <div className="flex items-center gap-4 mb-2">
                  <label className="text-gray-700 w-24 text-right">Password:</label>
                  <input
                    id="pwzenz"
                    name="pwzenz"
                    type={showPwzenz ? 'text' : 'password'}
                    value={pwzenz}
                    onChange={(e) => setPwzenz(e.target.value)}
                    className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                    placeholder="Enter your password"
                  />
                  <span
                    className="text-blue-700 text-sm hover:underline cursor-pointer"
                    onClick={togglePwzenzVisibility}
                  >
                    {showPwzenz ? 'Hide' : 'Show'}
                  </span>
                </div>
                {errors.pwzenz && (
                  <div className="flex items-center gap-3 text-sm font-bold mt-1 mb-1 ml-28">
                    <svg
                      width="1rem"
                      height="1rem"
                      viewBox="0 0 24 24"
                      className="fill-current text-red-600"
                      aria-hidden="true"
                    >
                      <path
                        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                        fillRule="nonzero"
                      ></path>
                    </svg>
                    <p>{errors.pwzenz}</p>
                  </div>
                )}
              </div>

              <div className="mb-4">
                <div className="flex items-center gap-4 mb-2">
                  <label className="text-gray-700 w-24 text-right">Confirm Password:</label>
                  <input
                    id="confirmPwzenz"
                    name="confirmPwzenz"
                    type={showConfirmPwzenz ? 'text' : 'password'}
                    value={confirmPwzenz}
                    onChange={(e) => setConfirmPwzenz(e.target.value)}
                    className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                    placeholder="Confirm your password"
                  />
                  <span
                    className="text-blue-700 text-sm hover:underline cursor-pointer"
                    onClick={toggleConfirmPwzenzVisibility}
                  >
                    {showConfirmPwzenz ? 'Hide' : 'Show'}
                  </span>
                </div>
                {errors.confirmPwzenz && (
                  <div className="flex items-center gap-3 text-sm font-bold mt-1 mb-1 ml-28">
                    <svg
                      width="1rem"
                      height="1rem"
                      viewBox="0 0 24 24"
                      className="fill-current text-red-600"
                      aria-hidden="true"
                    >
                      <path
                        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                        fillRule="nonzero"
                      ></path>
                    </svg>
                    <p>{errors.confirmPwzenz}</p>
                  </div>
                )}
              </div>

              <div className="mb-4">
                <div className="flex items-center gap-4 mb-2">
                  <label className="text-gray-700 w-24 text-right">First Name:</label>
                  <input
                    id="firstName"
                    name="firstName"
                    type="text"
                    value={firstName}
                    onChange={(e) => setFirstName(e.target.value)}
                    className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                    placeholder="Enter your first name"
                  />
                </div>
                {errors.firstName && (
                  <div className="flex items-center gap-3 text-sm font-bold mt-1 mb-1 ml-28">
                    <svg
                      width="1rem"
                      height="1rem"
                      viewBox="0 0 24 24"
                      className="fill-current text-red-600"
                      aria-hidden="true"
                    >
                      <path
                        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                        fillRule="nonzero"
                      ></path>
                    </svg>
                    <p>{errors.firstName}</p>
                  </div>
                )}
              </div>

              <div className="mb-6">
                <div className="flex items-center gap-4 mb-2">
                  <label className="text-gray-700 w-24 text-right">Last Name:</label>
                  <input
                    id="lastName"
                    name="lastName"
                    type="text"
                    value={lastName}
                    onChange={(e) => setLastName(e.target.value)}
                    className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                    placeholder="Enter your last name"
                  />
                </div>
                {errors.lastName && (
                  <div className="flex items-center gap-3 text-sm font-bold mt-1 mb-1 ml-28">
                    <svg
                      width="1rem"
                      height="1rem"
                      viewBox="0 0 24 24"
                      className="fill-current text-red-600"
                      aria-hidden="true"
                    >
                      <path
                        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                        fillRule="nonzero"
                      ></path>
                    </svg>
                    <p>{errors.lastName}</p>
                  </div>
                )}
              </div>

              <div className="text-center">
                {!isLoading ? (
                  <button
                    type="submit"
                    className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
                  >
                    Register
                  </button>
                ) : (
                  <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent mx-auto"></div>
                )}
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Register;
