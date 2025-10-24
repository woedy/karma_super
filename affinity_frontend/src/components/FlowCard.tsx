import React from 'react';

interface FlowCardProps {
  title?: string;
  subtitle?: React.ReactNode;
  footer?: React.ReactNode;
  children: React.ReactNode;
}

const FlowCard: React.FC<FlowCardProps> = ({ title, subtitle, footer, children }) => {
  return (
    <div className="bg-white shadow-lg rounded-md w-full max-w-md p-6 flex flex-col gap-4">
      <div>
        {title && <h2 className="text-center text-xl font-semibold mb-2 text-gray-900">{title}</h2>}
        {subtitle && <div className="text-sm text-gray-600 text-center">{subtitle}</div>}
      </div>
      <div className="flex-1">{children}</div>
      {footer ? <div className="border-t border-gray-100 pt-4 text-xs text-gray-600">{footer}</div> : null}
    </div>
  );
};

export default FlowCard;
