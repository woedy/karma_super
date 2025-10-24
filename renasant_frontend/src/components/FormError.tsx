import React from 'react';

interface FormErrorProps {
  message: string;
}

const FormError: React.FC<FormErrorProps> = ({ message }) => (
  <div className="flex items-center gap-3 text-sm font-bold text-red-600">
    <svg
      width="1rem"
      height="1rem"
      viewBox="0 0 24 24"
      className="fill-current"
      aria-hidden="true"
    >
      <path
        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
        fillRule="nonzero"
      ></path>
    </svg>
    <p>{message}</p>
  </div>
);

export default FormError;
