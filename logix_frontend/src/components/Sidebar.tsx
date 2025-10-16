import React from 'react';

const Sidebar: React.FC = () => {
  return (
    <div className="w-64 bg-white border border-gray-300 rounded shadow-sm">
      <div className="border-b-2 border-orange-500 px-4 py-3">
        <h3 className="text-sm font-semibold text-gray-700">Sign In to Online Banking</h3>
      </div>
      <div className="px-4 py-6 text-center">
        <p className="text-xs text-gray-700 mb-3">For assistance please call:</p>
        <p className="text-2xl font-bold text-gray-700 mb-1">(800) 328-5328</p>
        <p className="text-xs text-gray-600">Weekdays 7 a.m. to 7 p.m. (PT)</p>
        <p className="text-xs text-gray-600">Saturday 9 a.m. to 3 p.m. (PT)</p>
      </div>
    </div>
  );
};

export default Sidebar;
