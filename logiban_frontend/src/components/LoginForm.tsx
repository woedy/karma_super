import React from 'react';

const LoginForm: React.FC = () => {
  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className="bg-white border-b-2 border-teal-500 px-6 py-4">
        <h2 className="text-lg text-gray-700">Sign In – Welcome to Logix Smarter Banking</h2>
      </div>

      <div className="px-6 py-6 space-y-4">
        <div className="flex items-center gap-4">
          <label className="text-gray-700 w-24 text-right">Username:</label>
          <input
            type="text"
            className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
          />
          <a href="#" className="text-blue-700 text-sm hover:underline">Not Registered?</a>
        </div>

        <div className="flex items-center gap-4">
          <label className="text-gray-700 w-24 text-right">Password:</label>
          <input
            type="password"
            className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
          />
          <a href="#" className="text-blue-700 text-sm hover:underline">Forgot Password?</a>
        </div>

        <div className="flex justify-center pt-2">
          <button className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded">
            Sign-In
          </button>
        </div>
      </div>

      <div className="px-6 pb-6">
        <p className="text-xs text-gray-700 mb-2">
          For security reasons, never share your username, password, social security number, account number or other private data online, unless you are certain who you are providing that information to, and only share information through a secure webpage or site.
        </p>
        <div className="text-xs text-blue-700 space-x-2">
          <a href="#" className="hover:underline">Forgot Username?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Forgot Password?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Forgot Everything?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Locked Out?</a>
        </div>
      </div>

      <div className="bg-red-600 text-white px-6 py-6 flex items-start gap-4">
        <div className="flex-shrink-0 mt-1">
          <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center">
            <svg className="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
        </div>
        <div className="flex-1">
          <p className="text-sm mb-3">
            <strong>Online and Mobile Banking will be unavailable on Sunday, October 12 from 4 AM to 6 AM (PT) for maintenance. We apologize for any inconvenience.</strong>
          </p>
          <p className="text-sm">
            Logix will be closed on Monday, October 13 in observance of the federal holiday. We will be open on Tuesday, October 14.
          </p>
        </div>
      </div>
    </div>
  );
};

export default LoginForm;
