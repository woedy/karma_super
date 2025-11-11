import React from 'react';
const Header: React.FC = () => {
  return (
    <header className="bg-white border-b border-gray-200">
      <div className="max-w-6xl mx-auto flex flex-wrap items-center justify-between px-6 py-4 gap-4">
        <div className="flex items-center">
          <img src="/assets/fifththird-logo.svg" alt="Fifth Third Bank" className="h-12 w-auto" />
        </div>
        <div className="text-xs sm:text-sm text-gray-700 flex items-center gap-3 uppercase tracking-wide">
          <a href="#" className="hover:text-[#003087]">Customer Service</a>
          <span className="text-gray-300">|</span>
          <a href="#" className="hover:text-[#003087]">Branch &amp; ATM Locator</a>
        </div>
      </div>
    </header>
  );
};

export default Header;
