import React from 'react';

const Header: React.FC = () => {
  return (
    <header className="bg-white border-b border-gray-200 py-4">
      <div className="max-w-7xl mx-auto px-4 flex items-center justify-between">
        <div className="flex items-center">
          <div className="text-3xl font-bold">
            <span className="text-gray-700">Logix</span>
          </div>
          <div className="ml-1 text-xs leading-tight">
            <div className="text-orange-600 font-semibold">smarter</div>
            <div className="text-orange-600 font-semibold">banking</div>
          </div>
        </div>
        <div className="flex gap-2 text-sm text-gray-700">
          <a href="#" className="hover:underline">Sign In</a>
          <span>|</span>
          <a href="#" className="hover:underline">Register</a>
        </div>
      </div>
    </header>
  );
};

export default Header;
