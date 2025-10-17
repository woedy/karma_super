import React, { useState } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";

const LoginForm = () => {
  const [remember, setRemember] = useState(false);

  return (
    <div className="h-screen flex flex-col overflow-hidden">
      <div className="flex-1 bg-cover bg-center bg-no-repeat relative" style={{backgroundImage: 'url(/assets/background.jpeg)'}}>
        <div className="absolute inset-0 bg-black bg-opacity-30"></div>
        <div className="relative z-10 h-full flex flex-col">
          <Header />

          <div className="flex-1 flex items-center justify-center px-4 py-4">
            <div className="bg-white shadow-lg rounded-md w-full max-w-md p-6">
              <h2 className="text-center text-xl font-semibold mb-4">Login</h2>

              <div className="space-y-4">
                {/* Username */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Username</label>
                  <input
                    type="text"
                    className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-sm"
                  />
                </div>

                {/* Password */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Password</label>
                  <div className="relative">
                    <input
                      type="password"
                      className="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-sm"
                    />
                    <span className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer hover:text-gray-600">
                      👁️
                    </span>
                  </div>
                </div>

                {/* Remember toggle */}
                <div className="flex items-center space-x-2">
                  <input
                    id="remember"
                    type="checkbox"
                    checked={remember}
                    onChange={() => setRemember(!remember)}
                    className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                  />
                  <label htmlFor="remember" className="text-sm text-gray-700">Remember Username</label>
                </div>

                {/* Login button */}
                <button className="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition">
                  Log In
                </button>

                {/* Forgot link */}
                <p className="text-center text-sm mt-2">
                  <a href="#" className="text-purple-800 hover:underline">
                    Forgot your username or password?
                  </a>
                </p>

                {/* Register */}
                <button className="w-full border border-purple-900 text-purple-900 py-2 rounded-md mt-2 font-medium hover:bg-purple-50 transition">
                  Register for digital banking
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <Footer />
    </div>
  );
};

export default LoginForm;
