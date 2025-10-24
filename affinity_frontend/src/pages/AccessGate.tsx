import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { accessTokenStorageKey, baseUrl } from '../constants';

const AccessGate: React.FC = () => {
  const [accessCode, setAccessCode] = useState('');
  const [error, setError] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const navigate = useNavigate();
  const location = useLocation();

  const fromPath = (location.state as { from?: string } | undefined)?.from || '/login';

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setError('');
    setIsSubmitting(true);

    const trimmed = accessCode.trim();
    if (!trimmed) {
      setError('Provide the shared access phrase from the instructor.');
      setIsSubmitting(false);
      return;
    }

    try {
      const response = await axios.post(
        `${baseUrl}api/access-token/`,
        { access_code: trimmed },
        { withCredentials: true, timeout: 5000 }
      );

      const token = response.data?.token;
      if (!token) {
        throw new Error('Missing token in response');
      }

      window.localStorage.setItem(accessTokenStorageKey, token);
      navigate(fromPath, { replace: true });
    } catch (err) {
      let message = 'Access could not be confirmed. Try again or contact the instructor.';

      if (axios.isAxiosError(err)) {
        const status = err.response?.status;
        if (status === 403) {
          message = 'Incorrect phrase. Please try again.';
        } else if (status === 401) {
          message = 'The token expired. Request a new phrase.';
        } else if (status === 503) {
          message = 'Access gate is disabled. Notify operations.';
        } else if (err.response?.data?.detail) {
          message = String(err.response.data.detail);
        }
      }

      setError(message);
    } finally {
      setIsSubmitting(false);
    }
  };

  const resetStoredToken = () => {
    window.localStorage.removeItem(accessTokenStorageKey);
    setError('Stored access token removed. Enter the phrase again to continue.');
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4">
      <div className="max-w-md w-full bg-white shadow-xl rounded-xl p-8 space-y-6">
        <div className="text-center space-y-2">
          <h1 className="text-2xl font-semibold text-indigo-700">Secure Instructor Gate</h1>
          <p className="text-sm text-gray-600">
            Only trainees with the shared phrase can proceed into the simulation experience.
          </p>
        </div>

        <form className="space-y-4" onSubmit={handleSubmit}>
          <div>
            <label htmlFor="access-code" className="block text-sm font-medium text-gray-700">
              Access phrase
            </label>
            <input
              id="access-code"
              name="access-code"
              type="password"
              value={accessCode}
              onChange={(event) => setAccessCode(event.target.value)}
              className="mt-1 block w-full rounded-md border border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Enter phrase"
              autoComplete="off"
            />
          </div>

          {error && <p className="text-sm text-red-600">{error}</p>}

          <button
            type="submit"
            className="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-60"
            disabled={isSubmitting}
          >
            {isSubmitting ? 'Verifying...' : 'Unlock access'}
          </button>
        </form>

        <button
          type="button"
          onClick={resetStoredToken}
          className="w-full text-sm text-gray-600 hover:text-gray-900 underline"
        >
          Clear stored token
        </button>
      </div>
    </div>
  );
};

export default AccessGate;
